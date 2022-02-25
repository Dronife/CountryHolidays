<?php

namespace App\Entity;

use App\Repository\HolidayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Exclude;



/**
 * @ORM\Entity(repositoryClass=HolidayRepository::class)
 */
class Holiday
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity=Country::class, inversedBy="holidays")
     * @Exclude()
     */
    private $country_id;

    public function __construct()
    {
        $this->country_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Country>
     */
    public function getCountryId(): Collection
    {
        return $this->country_id;
    }

    public function addCountryId(Country $countryId): self
    {
        if (!$this->country_id->contains($countryId)) {
            $this->country_id[] = $countryId;
        }

        return $this;
    }

    public function removeCountryId(Country $countryId): self
    {
        $this->country_id->removeElement($countryId);

        return $this;
    }
}
