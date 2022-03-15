<?php

namespace App\Services;

use App\Interfaces\CountryApiClientInterface;
use App\Repository\CountryRepository;

class CountryApiClientService implements CountryApiClientInterface
{
    private CountryRepository $repository;

    public function __construct(
        CountryRepository $countryRepository
    ) {
        $this->repository = $countryRepository;
    }

    public function getCountries(): array
    {
        return array_map(function ($country) {
            return $country->getName();
        }, $this->repository->findAll());
    }
}
