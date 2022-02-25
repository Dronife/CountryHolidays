<?php

namespace App\Services;

use App\Entity\Country;
use App\Entity\Holiday;
use App\Interfaces\HolidayHelperInterface;
use App\Model\Holiday\Holiday as HolidayModel;
use App\Repository\CountryRepository;
use App\Repository\HolidayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HolidayHelper implements HolidayHelperInterface
{


//    private  $URL = "https://kayaposoft.com/enrico/json/v2.0/?action=getHolidaysForMonth&month={$month}&year=:year&country=:country";
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
        // $holidays = $this->repo->getHolidays($year, $countryName)
        // if $holidays !== null: return $holidays
        // $this->holidayService->getHolidays($year, $countryName)

        //if(!$this->countryHasHolidays($year, $countryName))

        $country = $this->countryRepository->findOneBy(['name' => $countryName]);
        if(count($country->getHolidays()) == 0)
            $this->addHolidaysToDatabase($year, $country);
        return $country->getholidays();
    }

    private function addHolidaysToDatabase($year, Country $country): void
    {

        $holidayModels = $this->converterHelper->getHolidayModels('GET', $this->getEndPoint($year, $country),'array<'.HolidayModel::class.'>');
            foreach($holidayModels as $holidayModel){
                $this->addNew($holidayModel->getDefaultName(), $holidayModel->getHolidayType(), $holidayModel->getDateTime());
            }

    }

    private function getEndPoint($year, $country):string
    {
        return $this->baseApiUrl.$this->yearCall."&year=".$year."&country=".$country->getCountryCode()."&holidayType=public_holiday";
    }

    public function addNew(string $name, string $type, \DateTimeInterface $date){
//        if($this->holidayExists($name, $type, $date))
        $holiday = $this->createOrFind($name, $type, $date);
        $this->country->addHoliday($holiday);
        $this->entityManager->flush();
    }

    private function createOrFind(string $name, string $type, \DateTimeInterface $date) : Holiday
    {
        return $this->holidayRepository->findOneBy(['name' => $name, 'type' => $type, 'date' => $date]) ?:
            $this->insertHoliday($name, $type, $date);

    }

    private function insertHoliday(string $name, string $type, \DateTimeInterface $date):Holiday
    {
        $holiday  = new Holiday();
        $holiday->setName($name)->setType($type)->setDate($date);
        $this->entityManager->persist($holiday);
        return $holiday;
    }

}