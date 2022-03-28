<?php

namespace App\Factory\Model;

use App\Model\Request\Holiday\HolidayRequestDateModel;

class HolidayRequestCheckDateModelFactory
{
    public function create($country, $date) : HolidayRequestDateModel
    {
        $countryDateTypeModel = (new HolidayRequestDateModel());
        $countryDateTypeModel->setCountry($country);
        $countryDateTypeModel->setDate($date);
        return $countryDateTypeModel;
    }
}
