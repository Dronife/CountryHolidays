<?php

namespace App\Interfaces;

interface HolidayHelperInterface
{
    public function getHolidaysByYearAndCountry($year, $countryName) ;
}