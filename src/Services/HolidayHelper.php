<?php

namespace App\Services;

use App\Entity\Country;
use App\Entity\Holiday;
use App\Interfaces\HolidayHelperInterface;
use App\Model\DayPublicHoliday;
use App\Model\HolidayModel;
use App\Repository\CountryRepository;
use App\Repository\HolidayRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HolidayHelper implements HolidayHelperInterface
{


    private $entityManager;
    private $holidayRepository;
    private $countryRepository;
    private $baseApiUrl;
    private $client;
    private ModelConverterHelper $converterHelper;

    public function __construct(HolidayRepository      $holidayRepository, CountryRepository $countryRepository,
                                EntityManagerInterface $entityManager, string $baseApiUrl,
                                HttpClientInterface    $client, ModelConverterHelper $converterHelper)
    {
        $this->entityManager = $entityManager;
        $this->holidayRepository = $holidayRepository;
        $this->countryRepository = $countryRepository;
        $this->baseApiUrl = $baseApiUrl;
        $this->client = $client;
        $this->converterHelper = $converterHelper;
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

        $holidayModels = $this->converterHelper->getModel('GET', $this->getEndPoint($year, $country), 'array<' . HolidayModel::class . '>');
        foreach ($holidayModels as $holidayModel) {

            $holiday = $this->holidayRepository->findOneOrCreate([
                'name' => $holidayModel->getDefaultName(),
                'type' => $holidayModel->getHolidayType(),
                'date' => $holidayModel->getDateTime(),
            ]);
            $country->addHoliday($holiday);
            $this->entityManager->flush();
        }

    }

    private function getEndPoint($year, $country): string
    {
        return $this->baseApiUrl . "getHolidaysForYear&year=" . $year . "&country=" . $country->getCountryCode() . "&holidayType=public_holiday";
    }

    private function getEndPointForDayCheck(Country $country, string $date): string
    {
        return $this->baseApiUrl . 'isPublicHoliday' . "&date=$date&country=" . $country->getCountryCode();
    }

    private function getEndPointForSpecificHolidayDate(Country $country, string $date)
    {
        return $this->baseApiUrl . "getHolidaysForDateRange&fromDate=$date&toDate=$date&country=" . $country->getCountryCode();

    }

    public function getHolidayEndPoint()
    {
//        return $this->baseApiUrl
    }

    public function getDateHolidayType($date, $countryName)
    {
        $country = $this->countryRepository->findOneBy(['name' => $countryName]);
        $holiday = $country->getHolidayByDate($date);

        if ($holiday)
            return ['status' => 'holiday'];

        if (!$this->converterHelper
            ->getModel('GET', $this->getEndPointForDayCheck($country, $date), DayPublicHoliday::class)
            ->isPublicHoliday()
        ) {
            if (Carbon::parse($date)->isWeekend())
                return ['status' => 'free day'];
            return ['status' => 'workday'];
        }

        $holidayModel = $this->converterHelper
            ->getModel('GET', $this->getEndPointForSpecificHolidayDate($country, $date), 'array<'.HolidayModel::class.'>')[0];

        $holiday = $this->holidayRepository->create([
            'name' => $holidayModel->getDefaultName(),
            'type' => $holidayModel->getHolidayType(),
            'date' => $holidayModel->getDateTime(),
        ]);
        $country->addHoliday($holiday);
        $this->entityManager->flush();
        return ['status' => 'holiday'];
    }


}