<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Oro\ORM\Query\AST\Functions\SimpleFunction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
 */
class Country implements TranslatableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country_code;

    /**
     * @ORM\ManyToMany(targetEntity=Holiday::class, mappedBy="countries")
     */
    private $holidays;

    public function __construct()
    {
        $this->holidays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->country_code;
    }

    public function setCountryCode(string $country_code): self
    {
        $this->country_code = $country_code;

        return $this;
    }

    /**
     * @return Collection<int, Holiday>
     */
    public function getHolidays(): Collection
    {
        return $this->holidays;
    }

    public function addHoliday(Holiday $holiday): self
    {
        if (!$this->holidays->contains($holiday)) {
            $this->holidays[] = $holiday;
            $holiday->addCountryId($this);
        }

        return $this;
    }

    public function removeHoliday(Holiday $holiday): self
    {
        if ($this->holidays->removeElement($holiday)) {
            $holiday->removeCountryId($this);
        }

        return $this;
    }

    public function getHolidayByDate(string $date) : ?Holiday
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('date',Carbon::parse($date)));
        return $this->holidays->matching($criteria)->first() ?: null;
    }


    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        // TODO: Implement trans() method.
    }
}
