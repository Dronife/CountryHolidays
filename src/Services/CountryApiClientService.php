<?php

namespace App\Services;

use App\Entity\Country;
use App\Factory\CountryFactory;
use App\Interfaces\CountryApiClientInterface;
use App\Model\Response\Country\CountryModel;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CountryApiClientService implements CountryApiClientInterface
{
    private $repository;
    private $apiRequest;
    private $entityManager;
    private const COUNTRY_URL = 'https://kayaposoft.com/enrico/json/v2.0/?action=getSupportedCountries';
    private CountryFactory $countryFactory;

    public function __construct(CountryRepository $countryRepository,ApiRequest $apiRequest,
    EntityManagerInterface $entityManager, CountryFactory $countryFactory)
    {
        $this->apiRequest = $apiRequest;
        $this->repository = $countryRepository;
        $this->entityManager = $entityManager;
        $this->countryFactory = $countryFactory;
    }

    public function getCountries(): array
    {
        $this->saveCountriesIfDoesNotExist();
        return  array_map(function($country){
            return $country->getName();
        },$this->repository->findAll());
    }

    private function saveCountriesIfDoesNotExist() : void
    {
        if ($this->repository->getCount() == 0) {
            $countryModels = $this->apiRequest->get(self::COUNTRY_URL, 'array<'.CountryModel::class.'>');
            foreach($countryModels as $countryModel){
                $country = $this->countryFactory->create($countryModel);
                $this->repository->findOneOrCreate($country);
            }
        }
    }



}