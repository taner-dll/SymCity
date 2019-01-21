<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiparisDetailRepository")
 */
class SiparisDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $inner_color;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $outer_color;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $rim_color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="smallint")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Siparis", inversedBy="detail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $head;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="siparisDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $currency = 'eur';

    /**
     * @ORM\Column(type="float")
     */
    private $euro_usd;




    public function __construct()
    {
        $this->product = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInnerColor(): ?int
    {
        return $this->inner_color;
    }

    public function setInnerColor(int $inner_color): self
    {
        $this->inner_color = $inner_color;

        return $this;
    }

    public function getOuterColor(): ?int
    {
        return $this->outer_color;
    }

    public function setOuterColor(int $outer_color): self
    {
        $this->outer_color = $outer_color;

        return $this;
    }

    public function getRimColor(): ?int
    {
        return $this->rim_color;
    }

    public function setRimColor(int $rim_color): self
    {
        $this->rim_color = $rim_color;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getHead(): ?Siparis
    {
        return $this->head;
    }

    public function setHead(?Siparis $head): self
    {
        $this->head = $head;

        return $this;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getEuroUsd(): ?float
    {
        return $this->euro_usd;
    }

    public function setEuroUsd(float $euro_usd): self
    {
        $this->euro_usd = $euro_usd;

        return $this;
    }


}
