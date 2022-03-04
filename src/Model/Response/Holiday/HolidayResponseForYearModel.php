<?php

namespace App\Model\Response\Holiday;

use App\Entity\Holiday;

class HolidayResponseForYearModel
{
    private Holiday $holidays;

    /**
     * @return array
     */
    public function getHolidays(): array
    {
        return [$this->holidays];
    }
}