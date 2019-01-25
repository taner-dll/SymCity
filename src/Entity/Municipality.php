<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MunicipalityRepository")
 */
class Municipality
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $about;

    /**
     * @ORM\Column(type="text")
     */
    private $mayor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $featured_picture;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $web;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $map;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $extra_info;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MunicipalityNews", mappedBy="municipality")
     */
    private $municipalityNews;

    public function __construct()
    {
        $this->municipalityNews = new ArrayCollection();
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

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getMayor(): ?string
    {
        return $this->mayor;
    }

    public function setMayor(string $mayor): self
    {
        $this->mayor = $mayor;

        return $this;
    }

    public function getFeaturedPicture(): ?string
    {
        return $this->featured_picture;
    }

    public function setFeaturedPicture(?string $featured_picture): self
    {
        $this->featured_picture = $featured_picture;

        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(?string $web): self
    {
        $this->web = $web;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getMap(): ?string
    {
        return $this->map;
    }

    public function setMap(?string $map): self
    {
        $this->map = $map;

        return $this;
    }

    public function getExtraInfo(): ?string
    {
        return $this->extra_info;
    }

    public function setExtraInfo(?string $extra_info): self
    {
        $this->extra_info = $extra_info;

        return $this;
    }

    /**
     * @return Collection|MunicipalityNews[]
     */
    public function getMunicipalityNews(): Collection
    {
        return $this->municipalityNews;
    }

    public function addMunicipalityNews(MunicipalityNews $municipalityNews): self
    {
        if (!$this->municipalityNews->contains($municipalityNews)) {
            $this->municipalityNews[] = $municipalityNews;
            $municipalityNews->setMunicipality($this);
        }

        return $this;
    }

    public function removeMunicipalityNews(MunicipalityNews $municipalityNews): self
    {
        if ($this->municipalityNews->contains($municipalityNews)) {
            $this->municipalityNews->removeElement($municipalityNews);
            // set the owning side to null (unless already changed)
            if ($municipalityNews->getMunicipality() === $this) {
                $municipalityNews->setMunicipality(null);
            }
        }

        return $this;
    }
}
