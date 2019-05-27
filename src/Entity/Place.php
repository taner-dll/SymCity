<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlaceRepository")
 */
class Place
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
    private $map;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $featured_image;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlacesToVisit", mappedBy="place")
     */
    private $ptv;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Municipality", mappedBy="place")
     */
    private $municipalities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="place")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Announce", mappedBy="place")
     */
    private $announces;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Advert", mappedBy="place")
     */
    private $adverts;

    public function __construct()
    {
        $this->ptv = new ArrayCollection();
        $this->municipalities = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->announces = new ArrayCollection();
        $this->adverts = new ArrayCollection();
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

    public function getMap(): ?string
    {
        return $this->map;
    }

    public function setMap(?string $map): self
    {
        $this->map = $map;

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
     * @return Collection|PlacesToVisit[]
     */
    public function getPtv(): Collection
    {
        return $this->ptv;
    }

    public function addPtv(PlacesToVisit $ptv): self
    {
        if (!$this->ptv->contains($ptv)) {
            $this->ptv[] = $ptv;
            $ptv->setPlace($this);
        }

        return $this;
    }

    public function removePtv(PlacesToVisit $ptv): self
    {
        if ($this->ptv->contains($ptv)) {
            $this->ptv->removeElement($ptv);
            // set the owning side to null (unless already changed)
            if ($ptv->getPlace() === $this) {
                $ptv->setPlace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Municipality[]
     */
    public function getMunicipalities(): Collection
    {
        return $this->municipalities;
    }

    public function addMunicipality(Municipality $municipality): self
    {
        if (!$this->municipalities->contains($municipality)) {
            $this->municipalities[] = $municipality;
            $municipality->setPlace($this);
        }

        return $this;
    }

    public function removeMunicipality(Municipality $municipality): self
    {
        if ($this->municipalities->contains($municipality)) {
            $this->municipalities->removeElement($municipality);
            // set the owning side to null (unless already changed)
            if ($municipality->getPlace() === $this) {
                $municipality->setPlace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setPlace($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getPlace() === $this) {
                $event->setPlace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Announce[]
     */
    public function getAnnounces(): Collection
    {
        return $this->announces;
    }

    public function addAnnounce(Announce $announce): self
    {
        if (!$this->announces->contains($announce)) {
            $this->announces[] = $announce;
            $announce->setPlace($this);
        }

        return $this;
    }

    public function removeAnnounce(Announce $announce): self
    {
        if ($this->announces->contains($announce)) {
            $this->announces->removeElement($announce);
            // set the owning side to null (unless already changed)
            if ($announce->getPlace() === $this) {
                $announce->setPlace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Advert[]
     */
    public function getAdverts(): Collection
    {
        return $this->adverts;
    }

    public function addAdvert(Advert $advert): self
    {
        if (!$this->adverts->contains($advert)) {
            $this->adverts[] = $advert;
            $advert->setPlace($this);
        }

        return $this;
    }

    public function removeAdvert(Advert $advert): self
    {
        if ($this->adverts->contains($advert)) {
            $this->adverts->removeElement($advert);
            // set the owning side to null (unless already changed)
            if ($advert->getPlace() === $this) {
                $advert->setPlace(null);
            }
        }

        return $this;
    }
}
