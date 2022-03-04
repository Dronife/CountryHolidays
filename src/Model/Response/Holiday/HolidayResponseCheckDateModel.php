<?php

namespace App\Model\Response\Holiday;

class HolidayResponseCheckDateModel
{
    private string $type;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

}