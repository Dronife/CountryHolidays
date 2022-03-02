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


    private $entityManager;
    private $holidayRepository;
    private $countryRepository;
    private $baseApiUrl;
    private $client;
    private $holidayFactoryService;
    private ModelConverterHelper $converterHelper;

    public function __construct(HolidayRepository      $holidayRepository, CountryRepository $countryRepository,
                                EntityManagerInterface $entityManager, string $baseApiUrl,
                                HttpClientInterface    $client, ModelConverterHelper $converterHelper,
                                HolidayFactoryService $holidayFactoryService)
    {
        $this->entityManager = $entityManager;
        $this->holidayRepository = $holidayRepository;
        $this->countryRepository = $countryRepository;
        $this->baseApiUrl = $baseApiUrl;
        $this->client = $client;
        $this->converterHelper = $converterHelper;
        $this->holidayFactoryService = $holidayFactoryService;
    }

    public function getHolidaysByYearAndCountry($year, $countryName): Collection
    {
        $country = $this->countryRepository->findOneBy(['name' => $countryName]);
        if (count($country->getHolidays()) == 0)
            $this->addAndAssignHolidaysToDatabase($year, $country);
        return $country->getholidays();
    }

    private function addAndAssignHolidaysToDatabase($year, Country $country): void
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

    private function getUrlForSpecificHolidayDate(Country $country, string $date) : string
    {
        return $this->baseApiUrl . "getHolidaysForDateRange&fromDate=$date&toDate=$date&country=" . $country->getCountryCode();
    }

    public function getDateHolidayType(string $date, string $countryName)
    {
        $country = $this->countryRepository->findOneBy(['name' => $countryName]);
        $holiday = $country->getHolidayByDate($date);
        if ($holiday) {
            return 'holiday';
        }

        if (!$this->isSelectedDateIsPublicHoliday($country, $date)) {
            if (Carbon::parse($date)->isWeekend())
                return 'free day';
            return 'workday';
        }

        $holidayModel = $this->converterHelper
            ->getModel('GET', $this->getUrlForSpecificHolidayDate($country, $date), 'array<'.HolidayModel::class.'>')[0];

        $holidayEntity = $this->holidayFactoryService->create($holidayModel);
        $holiday = $this->holidayRepository->create($holidayEntity);
        $country->addHoliday($holiday);
        $this->entityManager->flush();
        return 'holiday';
    }

    private function isSelectedDateIsPublicHoliday(Country $country, string $date) : bool
    {
        return $this->converterHelper
            ->getModel('GET', $this->getUrlForDayCheck($country, $date), DayPublicHoliday::class)
            ->isPublicHoliday();
    }


}