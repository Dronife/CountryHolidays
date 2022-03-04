<?php

namespace App\Services;

use App\Entity\Holiday;
use App\Model\Response\ApiClient\HolidayModel;

class HolidayFactoryService
{
    public function create(HolidayModel $holidayModel) : Holiday
    {
        $holiday = new Holiday();
        $holiday->setName($holidayModel->getDefaultName());
        $holiday->setType($holidayModel->getHolidayType());
        $holiday->setDate($holidayModel->getDateTime());
        return $holiday;
    }
}