<?php

namespace App\Factory\Model;

use App\Model\Request\Holiday\HolidayRequestDateModel;
use App\Model\Request\Holiday\HolidayWithDateRangeModel;

class HolidayWithDateRangeFactory
{
    public function create(HolidayRequestDateModel $dateModel) : HolidayWithDateRangeModel
    {
        $holidayWithDateRange = new HolidayWithDateRangeModel();
        $holidayWithDateRange->setCountry($dateModel->getCountry());
        $holidayWithDateRange->setStartDate($dateModel->getDate());
        $holidayWithDateRange->setEndDate($dateModel->getDate());
        return $holidayWithDateRange;
    }
}
