<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvertRepository")
 */
class Advert
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
    private $title;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_update;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $featured_image;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telephone;

    /**
     * @ORM\Column(type="integer")
     */
    private $confirm = 0;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $status = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Place", inversedBy="adverts")
     */
    private $place;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AdSubCategory", inversedBy="adverts")
     */
    private $sub_category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AdCategory", inversedBy="adverts")
     */
    private $category;

    /**
     * @ORM\Column(type="boolean")
     */
    private $secret_price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $secret_phone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $secret_email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Place")
     */
    private $sub_place;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->last_update;
    }

    public function setLastUpdate(\DateTimeInterface $last_update): self
    {
        $this->last_update = $last_update;

        return $this;
    }

    public function getFeaturedImage(): ?string
    {
        return $this->featured_image;
    }

    public function setFeaturedImage(?string $featured_image): self
    {
        $this->featured_image = $featured_image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getConfirm(): ?int
    {
        return $this->confirm;
    }

    public function setConfirm(int $confirm): self
    {
        $this->confirm = $confirm;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getSubCategory(): ?AdSubCategory
    {
        return $this->sub_category;
    }

    public function setSubCategory(?AdSubCategory $sub_category): self
    {
        $this->sub_category = $sub_category;

        return $this;
    }

    public function getCategory(): ?AdCategory
    {
        return $this->category;
    }

    public function setCategory(?AdCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSecretPrice(): ?bool
    {
        return $this->secret_price;
    }

    public function setSecretPrice(bool $secret_price): self
    {
        $this->secret_price = $secret_price;

        return $this;
    }

    public function getSecretPhone(): ?bool
    {
        return $this->secret_phone;
    }

    public function setSecretPhone(bool $secret_phone): self
    {
        $this->secret_phone = $secret_phone;

        return $this;
    }

    public function getSecretEmail(): ?bool
    {
        return $this->secret_email;
    }

    public function setSecretEmail(bool $secret_email): self
    {
        $this->secret_email = $secret_email;

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
