<?php

namespace App\Model\Response\KayaposoftApi;

class IsWorkDayModel implements KayaposoftApiModelInterface
{
    private bool $isWorkDay;

    /**
     * @return bool
     */
    public function isWorkDay(): bool
    {
        return $this->isWorkDay;
    }

    /**
     * @param bool $isWorkDay
     */
    public function setIsWorkDay(bool $isWorkDay): void
    {
        $this->isWorkDay = $isWorkDay;
    }
}
