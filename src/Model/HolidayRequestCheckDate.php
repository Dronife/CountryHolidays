<?php

namespace App\Model;

use Carbon\Carbon;
use DateTimeInterface;

class HolidayRequestCheckDate
{

    private DateTimeInterface $date;
    private string $country;

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

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


}