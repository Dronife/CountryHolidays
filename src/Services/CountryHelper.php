<?php

namespace App\Services;

use App\Entity\Country;
use App\Interfaces\CountryInterface;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CountryHelper implements CountryInterface
{
    private $repository;
    private $client;
    private $entityManager;
    public function __construct(CountryRepository $countryRepository, HttpClientInterface $client,
    EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->repository = $countryRepository;
        $this->entityManager = $entityManager;
    }

    public function getCountries(): iterable
    {
        $this->initData();
        return  array_map(function($country){
            return $country->getName();
        },$this->repository->findAll());
    }

    private function initData()
    {
        if ($this->repository->getCount() == 0) {
            $response = $this->client->request(
                'GET',
                'https://kayaposoft.com/enrico/json/v2.0/?action=getSupportedCountries'
            );
            $response->toArray();

            array_map(function ($object) {
                $this->addNew($object['fullName']);
            }, $response->toArray());
        }
    }

    public function addNew(string $name){
        $country = new Country();
        $country->setName($name);
        $this->entityManager->persist($country);
        $this->entityManager->flush();
    }


}