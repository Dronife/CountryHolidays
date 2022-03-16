<?php

namespace App\Message\Holiday;

use App\Entity\Country;
use App\Model\Response\ApiClient\HolidayModel;

class CreateAndAssignHoliday
{
    private HolidayModel $holidayModel;
    private Country $country;

    public function __construct(HolidayModel $holidayModel, Country $country)
    {
        $this->holidayModel = $holidayModel;
        $this->country = $country;
    }

    /**
     * @return HolidayModel
     */
    public function getHolidayModel(): HolidayModel
    {
        return $this->holidayModel;
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }
}
