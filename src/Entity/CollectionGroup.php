<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CollectionGroupRepository")
 */
class CollectionGroup
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
     * @ORM\OneToMany(targetEntity="App\Entity\ProductCollection", mappedBy="collectionGroup")
     */
    private $prodcollection;

    /**
     * @ORM\Column(type="smallint")
     */
    private $enable;

    public function __construct()
    {
        $this->prodcollection = new ArrayCollection();
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

    /**
     * @return Collection|ProductCollection[]
     */
    public function getProdcollection(): Collection
    {
        return $this->prodcollection;
    }

    public function addProdcollection(ProductCollection $prodcollection): self
    {
        if (!$this->prodcollection->contains($prodcollection)) {
            $this->prodcollection[] = $prodcollection;
            $prodcollection->setCollectionGroup($this);
        }

        return $this;
    }

    public function removeProdcollection(ProductCollection $prodcollection): self
    {
        if ($this->prodcollection->contains($prodcollection)) {
            $this->prodcollection->removeElement($prodcollection);
            // set the owning side to null (unless already changed)
            if ($prodcollection->getCollectionGroup() === $this) {
                $prodcollection->setCollectionGroup(null);
            }
        }

        return $this;
    }

    public function getEnable(): ?int
    {
        return $this->enable;
    }

    public function setEnable(int $enable): self
    {
        $this->enable = $enable;

        return $this;
    }
}
