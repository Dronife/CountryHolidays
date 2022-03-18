<?php

namespace App\Model\Response\KayaposoftApi;

use App\Model\Holiday\Date;
use App\Model\Holiday\Name;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;

class HolidayModel implements KayaposoftApiModelInterface
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

    public function getDefaultName(): string
    {
        $indexOfEnglishLanguage = 0;
        foreach ($this->getName() as $key => $title) {
            if ($title['lang'] == 'en') {
                $indexOfEnglishLanguage = $key;
                break;
            }
        }
        return  $this->getName()[$indexOfEnglishLanguage]['text'];
    }

    public function getDateTime(): \DateTimeInterface
    {
        return Carbon::parse($this->getDate()->getYear() . "-" . $this->getDate()->getMonth() . "-" . $this->getDate()->getDay());
    }

}
