<?php

namespace App\Services;

use App\Entity\Country;
use App\Entity\Holiday;
use App\Interfaces\HolidayHelperInterface;
use App\Model\HolidayModel;
use App\Repository\CountryRepository;
use App\Repository\HolidayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HolidayHelper implements HolidayHelperInterface
{


    private $entityManager;
    private $holidayRepository;
    private $countryRepository;
    private $baseApiUrl;
    private $client;
    private $yearCall = "getHolidaysForYear";
    private ModelConverterHelper $converterHelper;

    public function __construct(HolidayRepository $holidayRepository, CountryRepository $countryRepository,
                                EntityManagerInterface $entityManager,string $baseApiUrl,
                                HttpClientInterface $client, ModelConverterHelper $converterHelper)
    {
        $this->entityManager = $entityManager;
        $this->holidayRepository = $holidayRepository;
        $this->countryRepository = $countryRepository;
        $this->baseApiUrl = $baseApiUrl;
        $this->client = $client;
        $this->converterHelper = $converterHelper;
    }

    public function getHolidaysByYearAndCountry($year, $countryName)
    {
        $country = $this->countryRepository->findOneBy(['name' => $countryName]);
        if(count($country->getHolidays()) == 0)
            $this->addAndAssignHolidaysToDatabase($year, $country);
        return $country->getholidays();
    }

    private function addAndAssignHolidaysToDatabase($year, Country $country): void
    {

        $holidayModels = $this->converterHelper->getEntityModel('GET', $this->getEndPoint($year, $country),'array<'.HolidayModel::class.'>');
            foreach($holidayModels as $holidayModel){

                $holiday = $this->holidayRepository->findOneOrCreate([
                    'name' => $holidayModel->getDefaultName(),
                    'type' => $holidayModel->getHolidayType(),
                    'date' => $holidayModel->getDateTime(),
                ]);
                $country->addHoliday($holiday);
                $this->entityManager->flush();
            }

    }

    private function getEndPoint($year, $country):string
    {
        return $this->baseApiUrl.$this->yearCall."&year=".$year."&country=".$country->getCountryCode()."&holidayType=public_holiday";
    }


}