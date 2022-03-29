<?php

namespace App\Message\Holiday;

use App\Entity\Country;

class AddKayaposoftApiHolidaysToCountry
{
    private int $year;
    private Country $country;

    public function __construct(int $year, Country $country)
    {
        $this->year = $year;
        $this->country = $country;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }
}
