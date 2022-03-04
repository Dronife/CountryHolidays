<?php

namespace App\Model\Response\Holiday;

class HolidayResponseCountOfFreeDaysModel
{
    private int $count;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
}