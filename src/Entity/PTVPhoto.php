<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PTVPhotoRepository")
 */
class PTVPhoto
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
    private $file_name;

    /**
     * @ORM\Column(type="date")
     */
    private $date_added;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlacesToVisit", inversedBy="pTVPhotos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ptv;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    public function setFileName(string $file_name): self
    {
        $this->file_name = $file_name;

        return $this;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->date_added;
    }

    public function setDateAdded(\DateTimeInterface $date_added): self
    {
        $this->date_added = $date_added;

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
