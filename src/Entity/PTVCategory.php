<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PTVCategoryRepository")
 */
class PTVCategory
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
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlacesToVisit", mappedBy="pTVCategory")
     */
    private $ptv;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __construct()
    {
        $this->ptv = new ArrayCollection();
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

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return Collection|PlacesToVisit[]
     */
    public function getPtv(): Collection
    {
        return $this->ptv;
    }

    public function addPtv(PlacesToVisit $ptv): self
    {
        if (!$this->ptv->contains($ptv)) {
            $this->ptv[] = $ptv;
            $ptv->setPTVCategory($this);
        }

        return $this;
    }

    public function removePtv(PlacesToVisit $ptv): self
    {
        if ($this->ptv->contains($ptv)) {
            $this->ptv->removeElement($ptv);
            // set the owning side to null (unless already changed)
            if ($ptv->getPTVCategory() === $this) {
                $ptv->setPTVCategory(null);
            }
        }

        return $this;
    }


}