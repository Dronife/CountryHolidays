<?php

namespace App\Factory;

use App\Entity\Country;
use App\Model\Response\ApiClient\CountryModel;

class CountryFactory
{
    public function create(CountryModel $countryModel) : Country
    {
        $country = new Country();
        $country->setName($countryModel->getFullName());
        $country->setCountryCode($countryModel->getCountryCode());
        return $country;
    }

}