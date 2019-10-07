<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdSubCategoryRepository")
 */
class AdSubCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=50)
     */
    private $short_name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AdCategory", inversedBy="sub")
     * @ORM\JoinColumn(nullable=false)
     */
    private $adCategory;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Advert", mappedBy="sub_category")
     */
    private $adverts;

    public function __construct()
    {
        $this->adverts = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getShortName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getShortName(): ?string
    {
        return $this->short_name;
    }

    public function setShortName(string $short_name): self
    {
        $this->short_name = $short_name;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getAdCategory(): ?AdCategory
    {
        return $this->adCategory;
    }

    public function setAdCategory(?AdCategory $adCategory): self
    {
        $this->adCategory = $adCategory;

        return $this;
    }

    /**
     * @return Collection|Advert[]
     */
    public function getAdverts(): Collection
    {
        return $this->adverts;
    }

    public function addAdvert(Advert $advert): self
    {
        if (!$this->adverts->contains($advert)) {
            $this->adverts[] = $advert;
            $advert->setSubCategory($this);
        }

        return $this;
    }

    public function removeAdvert(Advert $advert): self
    {
        if ($this->adverts->contains($advert)) {
            $this->adverts->removeElement($advert);
            // set the owning side to null (unless already changed)
            if ($advert->getSubCategory() === $this) {
                $advert->setSubCategory(null);
            }
        }

        return $this;
    }
}
