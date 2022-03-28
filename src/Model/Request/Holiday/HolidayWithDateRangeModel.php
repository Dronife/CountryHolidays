<?php

namespace App\Model\Request\Holiday;

use App\Entity\Country;
use DateTimeInterface;

class HolidayWithDateRangeModel implements HolidayRequestInterface
{
    private DateTimeInterface $startDate;
    private DateTimeInterface $endDate;
    private Country $country;

    /**
     * @return DateTimeInterface
     */
    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function getStartDateWithFormat(string $format): string
    {
        return $this->startDate->format($format);
    }

    /**
     * @param DateTimeInterface $startDate
     */
    public function setStartDate(DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return DateTimeInterface
     */
    public function getEndDate(): DateTimeInterface
    {
        return $this->endDate;
    }


    public function getEndDateWithFormat(string $format): string
    {
        return $this->endDate->format($format);
    }

    /**
     * @param DateTimeInterface $endDate
     */
    public function setEndDate(DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
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
