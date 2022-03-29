<?php

namespace App\Message\Holiday;

use App\Entity\Country;
use App\Model\Response\KayaposoftApi\HolidayModel;
use App\Model\Response\KayaposoftApi\KayaposoftApiModelInterface;

class CreateAndAssignHoliday
{
    /**@var HolidayModel**/
    private KayaposoftApiModelInterface $holidayModel;
    private Country $country;

    public function __construct(KayaposoftApiModelInterface $holidayModel, Country $country)
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
