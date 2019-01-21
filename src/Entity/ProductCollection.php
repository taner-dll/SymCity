<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductCollectionRepository")
 */
class ProductCollection
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
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $model_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="collection")
     */
    private $product;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CollectionGroup", inversedBy="prodcollection")
     * @ORM\JoinColumn(nullable=false)
     */
    private $collectionGroup;

    /**
     * ProductCollection constructor.
     */
    public function __construct()
    {
        $this->product = new ArrayCollection();
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

    public function getModelCode(): ?string
    {
        return $this->model_code;
    }

    public function setModelCode(?string $model_code): self
    {
        $this->model_code = $model_code;

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

    /**
     * @return ArrayCollection
     */
    public function getProduct()
    {
        return $this->product;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
            $product->setCollection($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->product->contains($product)) {
            $this->product->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCollection() === $this) {
                $product->setCollection(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
       return $this->getName();
    }



    public function getCollectionGroup(): ?CollectionGroup
    {
        return $this->collectionGroup;
    }

    public function setCollectionGroup(?CollectionGroup $collectionGroup): self
    {
        $this->collectionGroup = $collectionGroup;

        return $this;
    }
}
