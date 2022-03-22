<?php

namespace App\Factory\Model;

use App\Model\Request\Holiday\HolidayRequestCheckDateModel;

class HolidayRequestCheckDateModelFactory
{
    public function create($country, $date) : HolidayRequestCheckDateModel
    {
        $countryDateTypeModel = (new HolidayRequestCheckDateModel());
        $countryDateTypeModel->setCountry($country);
        $countryDateTypeModel->setDate($date);
        return $countryDateTypeModel;
    }
}
