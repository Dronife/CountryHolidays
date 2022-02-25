<?php

namespace App\Model\Holiday;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;


class Holiday
{
    private Date $date;

    private ArrayCollection $name;
    private string $holidayType = '';

    /**
     * @return string
     */
    public function getHolidayType(): string
    {
        return $this->holidayType;
    }

    /**
     * @param string $holidayType
     */
    public function setHolidayType(string $holidayType): void
    {
        $this->holidayType = $holidayType;
    }

    /**
     * @return Date
     */
    public function getDate(): Date
    {
        return $this->date;
    }

    /**
     * @param Date $date
     */
    public function setDate(Date $date): void
    {
        $this->date = $date;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    public function getDefaultName():string{
        return $this->name[0]->getText();
    }

    /**
     * @param ArrayCollection<Name> $name
     */
    public function setName(ArrayCollection $name): void
    {
        $this->name = $name;
    }

    public function getDateTime() : \DateTimeInterface
    {
       return  Carbon::parse($this->getDate()->getYear()."-".$this->getDate()->getMonth()."-".$this->getDate()->getDay());
    }


}