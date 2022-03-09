<?php

namespace App\Services;

use App\Entity\Country;
use App\Entity\Holiday;
use App\Factory\HolidayFactory;
use App\Interfaces\CountryApiClientInterface;
use App\Interfaces\HolidayApiClientInterface;
use App\Model\Response\ApiClient\DayPublicHoliday;
use App\Model\Response\ApiClient\HolidayModel;
use App\Repository\CountryRepository;
use App\Repository\HolidayRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HolidayApiClientService implements HolidayApiClientInterface
{


    private EntityManagerInterface $entityManager;
    private HolidayRepository $holidayRepository;
    private CountryRepository $countryRepository;
    private string $baseApiUrl;
    private HttpClientInterface $client;
    private HolidayFactory $holidayFactory;
    private ApiRequest $apiRequest;
    private const TYPE_HOLIDAY = 'holiday';
    private const TYPE_FREE_DAY = 'free day';
    private const TYPE_WORKDAY = 'workday';

    public function __construct(
        HolidayRepository $holidayRepository,
        CountryRepository $countryRepository,
        EntityManagerInterface $entityManager,
        string $baseApiUrl,
        HttpClientInterface $client,
        ApiRequest $apiRequest,
        HolidayFactory $holidayFactory,
        CountryApiClientInterface $countryApiClient
    ) {
        $this->entityManager = $entityManager;
        $this->holidayRepository = $holidayRepository;
        $this->countryRepository = $countryRepository;
        $this->baseApiUrl = $baseApiUrl;
        $this->client = $client;
        $this->apiRequest = $apiRequest;
        $this->holidayFactory = $holidayFactory;
        $countryApiClient->saveCountriesIfDoesNotExist();
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

    public function getDateHolidayType(string $date, Country $country): string
    {
        $holiday = $country->getHolidayByDate($date);
        if ($holiday) {
            return self::TYPE_HOLIDAY;
        }

        if (!$this->isSelectedDateIsPublicHoliday($country, $date)) {
            if (Carbon::parse($date)->isWeekend()) {
                return self::TYPE_FREE_DAY;
            }
            return self::TYPE_WORKDAY;
        }

        $holidayModel = $this->apiRequest
            ->get($this->getUrlForSpecificHolidayDate($country, $date), 'array<' . HolidayModel::class . '>')[0];

        $this->createAndAssignHoliday($holidayModel, $country);
        return self::TYPE_HOLIDAY;
    }

    public function getCountOfFreeDaysAndHolidays(string $year, Country $country): int
    {
//        if (count($holidays) == 0)
        $this->addHolidaysToCountry($year, $country);
        $holidays = $this->holidayRepository->getHolidaysByYearAndCountryName($year, $country->getName());
        return $this->getCountedFreeDays($holidays);
    }

    private function addHolidaysToCountry($year, Country $country): void
    {
        /** @var HolidayModel[] $holidayModels */
        $holidays = $this->holidayRepository->getHolidaysByYearAndCountryName($year, $country->getName());
        if (count($holidays) > 0) {
            return;
        }
        $holidayModels = $this->apiRequest->get(
            $this->getHolidayForYearUrl($year, $country),
            'array<' . HolidayModel::class . '>'
        );
        foreach ($holidayModels as $holidayModel) {
            $this->createAndAssignHoliday($holidayModel, $country);
        }
    }

    private function createAndAssignHoliday(HolidayModel $holidayModel, Country $country): void
    {
        $holidayEntity = $this->holidayFactory->create($holidayModel);
        $holiday = $this->holidayRepository->findOneOrCreate($holidayEntity);
        $country->addHoliday($holiday);
        $this->entityManager->flush();
    }

    private function getHolidayForYearUrl($year, $country): string
    {
        return $this->baseApiUrl . "getHolidaysForYear&year=" . $year . "&country=" . $country->getCountryCode(
            ) . "&holidayType=public_holiday";
    }

    private function getUrlForDayCheck(Country $country, string $date): string
    {
        return $this->baseApiUrl . 'isPublicHoliday' . "&date=$date&country=" . $country->getCountryCode();
    }

    private function getUrlForSpecificHolidayDate(Country $country, string $date): string
    {
        return $this->baseApiUrl . "getHolidaysForDateRange&fromDate=$date&toDate=$date&country=" . $country->getCountryCode(
            );
    }

    private function isSelectedDateIsPublicHoliday(Country $country, string $date): bool
    {
        return $this->apiRequest
            ->get($this->getUrlForDayCheck($country, $date), DayPublicHoliday::class)
            ->isPublicHoliday();
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

        return $count + $maxFreeDays;
    }


}
