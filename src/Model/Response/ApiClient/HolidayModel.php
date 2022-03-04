<?php

namespace App\Model\Response\ApiClient;

use App\Model\Holiday\Date;
use App\Model\Holiday\Name;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;

class HolidayModel
{

    private Date $date;
    private ArrayCollection $name;
    private string $holidayType;

    /**
     * @return ArrayCollection
     */
    public function getName(): ArrayCollection
    {
        return $this->name;
    }

    /**
     * @param ArrayCollection<Name> $name
     */
    public function setName(ArrayCollection $name): void
    {
        $this->name = $name;
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

    public function getDefaultName() : string
    {
        return $this->getName()[1]['text'];
    }

    public function getDateTime() : \DateTimeInterface
    {
        return Carbon::parse($this->getDate()->getYear()."-".$this->getDate()->getMonth()."-".$this->getDate()->getDay());
    }

}