<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdCategoryRepository")
 */
class AdCategory
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
     * @ORM\OneToMany(targetEntity="App\Entity\AdSubCategory", mappedBy="adCategory")
     */
    private $sub;

    public function __toString() {
        return $this->short_name;
    }

    public function __construct()
    {
        $this->sub = new ArrayCollection();
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

    /**
     * @return Collection|AdSubCategory[]
     */
    public function getSub(): Collection
    {
        return $this->sub;
    }

    public function addSub(AdSubCategory $sub): self
    {
        if (!$this->sub->contains($sub)) {
            $this->sub[] = $sub;
            $sub->setAdCategory($this);
        }

        return $this;
    }

    public function removeSub(AdSubCategory $sub): self
    {
        if ($this->sub->contains($sub)) {
            $this->sub->removeElement($sub);
            // set the owning side to null (unless already changed)
            if ($sub->getAdCategory() === $this) {
                $sub->setAdCategory(null);
            }
        }

        return $this;
    }
}
