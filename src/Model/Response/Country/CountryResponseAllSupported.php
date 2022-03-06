<?php

namespace App\Model\Response\Country;

class CountryResponseAllSupported
{
    private array $countries;

    /**
     * @return array
     */
    public function getCountries(): array
    {
        return [$this->countries];
    }

}