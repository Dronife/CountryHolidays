<?php

namespace App\Interfaces;

interface CountryApiClientInterface
{
    public function getCountries() : array;
}
