<?php

namespace App\Model\Request\Holiday;

use App\Entity\Country;
use DateTimeInterface;

class HolidayRequestCheckDate
{

    private DateTimeInterface $date;
    private Country $country;



    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param DateTimeInterface $date
     */
    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getDateByFormat($format):string
    {
        return $this->date->format($format);
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }


}