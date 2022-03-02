<?php

namespace App\Interfaces;

use App\Entity\Country;

interface HolidayApiClientInterface
{
    public function getHolidaysByYearAndCountry($year, Country $country) ;

    public function getDateHolidayType(string $year, string $countryName);

    public function getCountOfFreeDaysAndHolidays(string $year, Country $country);
}