<?php

namespace App\Services;

use App\Entity\Country;
use App\Entity\Holiday;
use App\Interfaces\HolidayApiClientInterface;
use App\Model\DayPublicHoliday;
use App\Model\HolidayModel;
use App\Repository\CountryRepository;
use App\Repository\HolidayRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HolidayApiClientService implements HolidayApiClientInterface
{


    private EntityManagerInterface $entityManager;
    private HolidayRepository $holidayRepository;
    private CountryRepository $countryRepository;
    private string $baseApiUrl;
    private HttpClientInterface $client;
    private HolidayFactoryService $holidayFactoryService;
    private ModelConverterHelper $converterHelper;
    private const TYPE_HOLIDAY = 'holiday';
    private const TYPE_FREE_DAY = 'free day';
    private const TYPE_WORKDAY = 'workday';
    private const DEFAULT_DATE_FORMAT = 'Y-m-d';


    public function __construct(HolidayRepository      $holidayRepository, CountryRepository $countryRepository,
                                EntityManagerInterface $entityManager, string $baseApiUrl,
                                HttpClientInterface    $client, ModelConverterHelper $converterHelper,
                                HolidayFactoryService  $holidayFactoryService)
    {
        $this->entityManager = $entityManager;
        $this->holidayRepository = $holidayRepository;
        $this->countryRepository = $countryRepository;
        $this->baseApiUrl = $baseApiUrl;
        $this->client = $client;
        $this->converterHelper = $converterHelper;
        $this->holidayFactoryService = $holidayFactoryService;
    }

    public function getHolidaysByYearAndCountry(int $year, Country $country): array
    {
        $holidays = $this->holidayRepository->getHolidaysByYearAndCountryName($year, $country->getName());
        if (count($holidays) == 0) {
            $this->addHolidaysToCountry($year, $country);
            return $this->holidayRepository->getHolidaysByYearAndCountryName($year, $country->getName());
        }
        return $holidays;
    }

    private function addHolidaysToCountry($year, Country $country): void
    {
        /** @var HolidayModel[] $holidayModels */
        $holidayModels = $this->converterHelper->getModel('GET', $this->getHolidayForYearUrl($year, $country), 'array<' . HolidayModel::class . '>');
        foreach ($holidayModels as $holidayModel) {
            $holidayEntity = $this->holidayFactoryService->create($holidayModel);
            $holiday = $this->holidayRepository->findOneOrCreate($holidayEntity);
            $country->addHoliday($holiday);
            $this->entityManager->flush();
        }
    }

    private function getHolidayForYearUrl($year, $country): string
    {
        return $this->baseApiUrl . "getHolidaysForYear&year=" . $year . "&country=" . $country->getCountryCode() . "&holidayType=public_holiday";
    }

    private function getUrlForDayCheck(Country $country, string $date): string
    {
        return $this->baseApiUrl . 'isPublicHoliday' . "&date=$date&country=" . $country->getCountryCode();
    }

    private function getUrlForSpecificHolidayDate(Country $country, string $date): string
    {
        return $this->baseApiUrl . "getHolidaysForDateRange&fromDate=$date&toDate=$date&country=" . $country->getCountryCode();
    }

    public function getDateHolidayType(string $date, Country $country) : string
    {
        $holiday = $country->getHolidayByDate($date);
        if ($holiday) {
            return self::TYPE_HOLIDAY;
        }

        if (!$this->isSelectedDateIsPublicHoliday($country, $date)) {
            if (Carbon::parse($date)->isWeekend())
                return self::TYPE_FREE_DAY;
            return self::TYPE_WORKDAY;
        }

        $holidayModel = $this->converterHelper
            ->getModel('GET', $this->getUrlForSpecificHolidayDate($country, $date), 'array<' . HolidayModel::class . '>')[0];

        $holidayEntity = $this->holidayFactoryService->create($holidayModel);
        $holiday = $this->holidayRepository->create($holidayEntity);
        $country->addHoliday($holiday);
        $this->entityManager->flush();
        return self::TYPE_HOLIDAY;
    }

    private function isSelectedDateIsPublicHoliday(Country $country, string $date): bool
    {
        return $this->converterHelper
            ->getModel('GET', $this->getUrlForDayCheck($country, $date), DayPublicHoliday::class)
            ->isPublicHoliday();
    }

    public function getCountOfFreeDaysAndHolidays(string $year, Country $country) : int
    {
        $holidays = $this->holidayRepository->getHolidaysByYearAndCountryName($year, $country->getName());
        if (count($holidays) == 0)
            $this->addHolidaysToCountry($year, $country);
        return $this->getCountedFreeDays($holidays);
    }

    /**
     * @param Holiday[] $holidays
     * @return int
     */
    private function getCountedFreeDays(array $holidays): int
    {
        $maxFreeDays = 0;
        $count = 0;
        $streakStartDate = null;
        $streakEndDate = null;
        $streakStarted = false;
        for ($holidayIndex = 1; $holidayIndex < count($holidays); $holidayIndex++) {
            $date0 = Carbon::parse($holidays[$holidayIndex - 1]->getDate());
            $date1 = Carbon::parse($holidays[$holidayIndex]->getDate());
            $dayDifference = $date0->diffInDays($date1);

            if ($dayDifference == 1) {
                if (!$streakStarted) {
                    $streakStartDate = $date0;
                    $streakStarted = true;
                }
                $count++;
            }

            if ($maxFreeDays < $count) {
                $maxFreeDays = $count;
            }

            if ($dayDifference != 1 || $holidayIndex == count($holidays) - 1) {
                $count++;
                if ($streakStarted) {
                    $streakEndDate = $dayDifference == 1 ? $date1 : $date0;
                    $streakStarted = false;
                }
                if ($maxFreeDays < $count) {
                    $maxFreeDays = $count;
                }
                $count = 0;

            }
        }
        $continueToCountWeekendForward = true;
        $continueToCountWeekendBackward = true;
        for ($i = 0; $i < 2; $i++) {
            if ($streakStartDate->subDay(1)->isWeekend() && $continueToCountWeekendBackward) {
                $count++;
            } else {
                $continueToCountWeekendBackward = false;
            }

            if ($streakEndDate->addDay()->isWeekend() && $continueToCountWeekendForward) {
                $count++;
            } else {
                $continueToCountWeekendForward = false;
            }
        }

        return $count+$maxFreeDays;
    }


}