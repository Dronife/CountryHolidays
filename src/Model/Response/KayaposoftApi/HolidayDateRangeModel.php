<?php

namespace App\Model\Response\KayaposoftApi;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
class HolidayDateRangeModel implements KayaposoftApiModelInterface
{
    /**
     * @JMS\Type("array<HolidayModel>")
     **/
    private array $data;

    /**
     * @return HolidayModel[]
     */
    public function getHolidayModels(): array
    {
        return $this->data;
    }

    /**
     * @param HolidayModel[] $holidayModels
     */
    public function setHolidayModels(array $holidayModels): void
    {

        $this->data = $holidayModels;
    }
}
