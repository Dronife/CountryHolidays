<?php

namespace App\Services;

use App\Interfaces\CountryHelperInterface;
use App\Interfaces\HolidayHelperInterface;
use App\Repository\CountryRepository;
use App\Repository\HolidayRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class HolidayHelper implements HolidayHelperInterface
{

    private $entityManager;

    private $holidayRepository;

    private $countryRepository;

    public function __construct(HolidayRepository $holidayRepository, CountryRepository $countryRepository, EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager;
        $this->holidayRepository = $holidayRepository;
        $this->countryRepository = $countryRepository;
    }

    public function getHolidaysByYearAndCountry($year, $countryName)
    {
//        return $this->countryRepository->findAll();
        return array_map(function($obj){return $obj->getName();},$this->countryRepository->findAll());

//        return $this->checkIfRecordExists($year, $countryName);
    }
//
//    private function checkIfRecordExists($year, $countryName)
//    {
//
////        dump($countryName);
//        return $this->countryRepository->findAll();
////        return $this->countryRepository->findBy(['name' => $countryName]);
//    }

}