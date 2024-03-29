<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlacesToVisitRepository")
 */
class PlacesToVisit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $about;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $maps;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $web;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $featured_picture;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PTVPhoto", mappedBy="ptv", orphanRemoval=true)
     */
    private $pTVPhotos;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Place", inversedBy="ptv")
     * @ORM\JoinColumn(nullable=true)
     */
    private $place;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PTVCategory", inversedBy="ptv")
     */
    private $pTVCategory;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $maps_embed_str;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Place", inversedBy="placesToVisits")
     */
    private $sub_place;

    public function __construct()
    {
        $this->pTVPhotos = new ArrayCollection();
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

    public function getMaps(): ?string
    {
        return $this->maps;
    }

    public function setMaps(?string $maps): self
    {
        $this->maps = $maps;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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

    /**
     * @return Collection|PTVPhoto[]
     */
    public function getPTVPhotos(): Collection
    {
        return $this->pTVPhotos;
    }

    public function addPTVPhoto(PTVPhoto $pTVPhoto): self
    {
        if (!$this->pTVPhotos->contains($pTVPhoto)) {
            $this->pTVPhotos[] = $pTVPhoto;
            $pTVPhoto->setPtv($this);
        }

        return $this;
    }

    public function removePTVPhoto(PTVPhoto $pTVPhoto): self
    {
        if ($this->pTVPhotos->contains($pTVPhoto)) {
            $this->pTVPhotos->removeElement($pTVPhoto);
            // set the owning side to null (unless already changed)
            if ($pTVPhoto->getPtv() === $this) {
                $pTVPhoto->setPtv(null);
            }
        }

        return $this;
    }



    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPTVCategory(): ?PTVCategory
    {
        return $this->pTVCategory;
    }

    public function setPTVCategory(?PTVCategory $pTVCategory): self
    {
        $this->pTVCategory = $pTVCategory;

        return $this;
    }

    public function getMapsEmbedStr(): ?string
    {
        return $this->maps_embed_str;
    }

    public function setMapsEmbedStr(?string $maps_embed_str): self
    {
        $this->maps_embed_str = $maps_embed_str;

        return $this;
    }

    public function getSubPlace(): ?Place
    {
        return $this->sub_place;
    }

    public function setSubPlace(?Place $sub_place): self
    {
        $this->sub_place = $sub_place;

        return $this;
    }

}