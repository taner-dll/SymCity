<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PTVComment", mappedBy="owner", cascade={"remove"})
     */
    private $pTVComments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="user", cascade={"remove"})
     */
    private $event;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Announce", mappedBy="user", cascade={"remove"})
     */
    private $announce;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Advert", mappedBy="user", cascade={"remove"})
     */
    private $adverts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Business", mappedBy="user", cascade={"remove"})
     */
    private $businesses;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $full_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user_name;

    /**
     * @ORM\Column(type="integer")
     */
    private $confirmed = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmation_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passwd_reset_code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwd_reset_due_date;

    public function __construct()
    {
        $this->pTVComments = new ArrayCollection();
        $this->event = new ArrayCollection();
        $this->announce = new ArrayCollection();
        $this->adverts = new ArrayCollection();
        $this->businesses = new ArrayCollection();
        $this->roles = array('ROLE_USER');
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|PTVComment[]
     */
    public function getPTVComments(): Collection
    {
        return $this->pTVComments;
    }

    public function addPTVComment(PTVComment $pTVComment): self
    {
        if (!$this->pTVComments->contains($pTVComment)) {
            $this->pTVComments[] = $pTVComment;
            $pTVComment->setOwner($this);
        }

        return $this;
    }

    public function removePTVComment(PTVComment $pTVComment): self
    {
        if ($this->pTVComments->contains($pTVComment)) {
            $this->pTVComments->removeElement($pTVComment);
            // set the owning side to null (unless already changed)
            if ($pTVComment->getOwner() === $this) {
                $pTVComment->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvent(): Collection
    {
        return $this->event;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->event->contains($event)) {
            $this->event[] = $event;
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->event->contains($event)) {
            $this->event->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Announce[]
     */
    public function getAnnounce(): Collection
    {
        return $this->announce;
    }

    public function addAnnounce(Announce $announce): self
    {
        if (!$this->announce->contains($announce)) {
            $this->announce[] = $announce;
            $announce->setUser($this);
        }

        return $this;
    }

    public function removeAnnounce(Announce $announce): self
    {
        if ($this->announce->contains($announce)) {
            $this->announce->removeElement($announce);
            // set the owning side to null (unless already changed)
            if ($announce->getUser() === $this) {
                $announce->setUser(null);
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
            $advert->setUser($this);
        }

        return $this;
    }

    public function removeAdvert(Advert $advert): self
    {
        if ($this->adverts->contains($advert)) {
            $this->adverts->removeElement($advert);
            // set the owning side to null (unless already changed)
            if ($advert->getUser() === $this) {
                $advert->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Business[]
     */
    public function getBusinesses(): Collection
    {
        return $this->businesses;
    }

    public function addBusiness(Business $business): self
    {
        if (!$this->businesses->contains($business)) {
            $this->businesses[] = $business;
            $business->setUser($this);
        }

        return $this;
    }

    public function removeBusiness(Business $business): self
    {
        if ($this->businesses->contains($business)) {
            $this->businesses->removeElement($business);
            // set the owning side to null (unless already changed)
            if ($business->getUser() === $this) {
                $business->setUser(null);
            }
        }

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $full_name): self
    {
        $this->full_name = $full_name;

        return $this;
    }

    public function setUserName(string $user_name): self
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getConfirmed(): ?int
    {
        return $this->confirmed;
    }

    public function setConfirmed(int $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getConfirmationCode(): ?string
    {
        return $this->confirmation_code;
    }

    public function setConfirmationCode(?string $confirmation_code): self
    {
        $this->confirmation_code = $confirmation_code;

        return $this;
    }

    public function getPasswdResetCode(): ?string
    {
        return $this->passwd_reset_code;
    }

    public function setPasswdResetCode(?string $passwd_reset_code): self
    {
        $this->passwd_reset_code = $passwd_reset_code;

        return $this;
    }

    public function getPasswdResetDueDate(): ?\DateTimeInterface
    {
        return $this->passwd_reset_due_date;
    }

    public function setPasswdResetDueDate(?\DateTimeInterface $passwd_reset_due_date): self
    {
        $this->passwd_reset_due_date = $passwd_reset_due_date;

        return $this;
    }
}
