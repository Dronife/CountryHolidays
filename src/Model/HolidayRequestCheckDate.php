<?php

namespace App\Model;

use Carbon\Carbon;
use DateTimeInterface;

class HolidayRequestCheckDate
{

    private string $date;
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
        return Carbon::parse($this->date);
    }

    /**
     * @param DateTimeInterface $date
     */
    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date->format('yyyy-MM-dd');
    }


}