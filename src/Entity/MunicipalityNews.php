<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MunicipalityNewsRepository")
 */
class MunicipalityNews
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
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $featured_image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $source_of_news_url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Municipality", inversedBy="municipalityNews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $municipality;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getSourceOfNewsUrl(): ?string
    {
        return $this->source_of_news_url;
    }

    public function setSourceOfNewsUrl(?string $source_of_news_url): self
    {
        $this->source_of_news_url = $source_of_news_url;

        return $this;
    }

    public function getMunicipality(): ?Municipality
    {
        return $this->municipality;
    }

    public function setMunicipality(?Municipality $municipality): self
    {
        $this->municipality = $municipality;

        return $this;
    }
}
