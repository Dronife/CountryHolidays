<?php

namespace App\Factory;

use App\Entity\Holiday;
use App\Model\Response\KayaposoftApi\HolidayModel;

class HolidayFactory
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
