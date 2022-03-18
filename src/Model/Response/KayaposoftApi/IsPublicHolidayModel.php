<?php

namespace App\Model\Response\KayaposoftApi;

class IsPublicHolidayModel implements KayaposoftApiModelInterface
{
    private bool $isPublicHoliday;

    /**
     * @return bool
     */
    public function isPublicHoliday(): bool
    {
        return $this->isPublicHoliday;
    }

    /**
     * @param bool $isPublicHoliday
     */
    public function setIsPublicHoliday(bool $isPublicHoliday): void
    {
        $this->isPublicHoliday = $isPublicHoliday;
    }
}
