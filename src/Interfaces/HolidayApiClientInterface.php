<?php

namespace App\Interfaces;

interface HolidayApiClientInterface
{
    public function getHolidaysByYearAndCountry($year, $countryName) ;

    public function getDateHolidayType(string $year, string $countryName);
}