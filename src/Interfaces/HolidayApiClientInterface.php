<?php

namespace App\Interfaces;

use App\Entity\Country;

interface HolidayApiClientInterface
{
    public function getHolidaysByYearAndCountry(int $year, Country $country) ;

    public function getDateHolidayType(string $date, Country $country);

    public function getCountOfFreeDaysAndHolidays(string $year, Country $country);
}