<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $property;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $properties;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $packaging;

    /**
     * @ORM\ManyToOne(targetEntity="ProductCollection", inversedBy="product")
     */
    private $collection;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SiparisDetail", mappedBy="product")
     */
    private $siparisDetails;

    public function __construct()
    {
        $this->siparisDetails = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function setProperty(string $property): self
    {
        $this->property = $property;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPrice(): ?float
    {
        $session = new Session();
        if ($session->get('currency')):

            if ($session->get('currency') == 'usd'):
                return number_format($this->price * floatval($session->get('parite')), 2);
            else:
                return $this->price;
            endif;

        else:
            return $this->price;
        endif;

    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getProperties(): ?string
    {
        return $this->properties;
    }

    public function setProperties(?string $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function getPackaging(): ?string
    {
        return $this->packaging;
    }

    public function setPackaging(?string $packaging): self
    {
        $this->packaging = $packaging;

        return $this;
    }

    public function getCollection(): ?ProductCollection
    {
        return $this->collection;
    }

    public function setCollection(?ProductCollection $collection): self
    {
        $this->collection = $collection;

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
     * @return ArrayCollection
     */
    public function getSiparisDetails(): ArrayCollection
    {
        return $this->siparisDetails;
    }

    public function addSiparisDetail(SiparisDetail $siparisDetail): self
    {
        if (!$this->siparisDetails->contains($siparisDetail)) {
            $this->siparisDetails[] = $siparisDetail;
            $siparisDetail->setProduct($this);
        }

        return $this;
    }

    public function removeSiparisDetail(SiparisDetail $siparisDetail): self
    {
        if ($this->siparisDetails->contains($siparisDetail)) {
            $this->siparisDetails->removeElement($siparisDetail);
            // set the owning side to null (unless already changed)
            if ($siparisDetail->getProduct() === $this) {
                $siparisDetail->setProduct(null);
            }
        }

        return $this;
    }


}
