<?php

namespace App\Interfaces;

interface HolidayHelperInterface
{
    public function getHolidaysByYearAndCountry($year, $countryName) ;

    public function getDateHolidayType($year, $countryName);
}