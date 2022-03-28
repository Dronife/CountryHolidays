<?php

namespace App\Model\Response\KayaposoftApi;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
class HolidayDateRangeModel extends AbstractArrayModel
{
    private array $holidayModels;

    public function __construct(){
        parent::__construct();
    }

    /**
     * @return HolidayModel[]
     */
    public function getHolidayModels(): array
    {
        return $this->holidayModels;
    }

    /**
     * @param HolidayModel[] $objectCollection
     */
    public function setArray(array $objectCollection)
    {
        $this->holidayModels = $objectCollection;
    }

    public function getFirst() : ?HolidayModel
    {
        return count($this->holidayModels) > 0 ? $this->holidayModels[0] : null;
    }
}
