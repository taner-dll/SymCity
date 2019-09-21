<?php

namespace App\Entity;

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
}
