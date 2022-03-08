<?php

namespace App\Services;

use App\Factory\CountryFactory;
use App\Interfaces\CountryApiClientInterface;
use App\Model\Response\ApiClient\CountryModel;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CountryApiClientService implements CountryApiClientInterface
{
    private CountryRepository $repository;
    private ApiRequest $apiRequest;
    private EntityManagerInterface $entityManager;
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

    public function saveCountriesIfDoesNotExist() : void
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