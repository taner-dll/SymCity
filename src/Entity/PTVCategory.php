<?php

namespace App\Entity;

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
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlacesToVisit", inversedBy="pTVCategories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ptv;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPtv(): ?PlacesToVisit
    {
        return $this->ptv;
    }

    public function setPtv(?PlacesToVisit $ptv): self
    {
        $this->ptv = $ptv;

        return $this;
    }
}
