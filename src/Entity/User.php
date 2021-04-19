<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
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


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $gender = 'none';

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $gsm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registration_date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FeedBack", mappedBy="user", orphanRemoval=true)
     */
    private $feedBacks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleClaps", mappedBy="user")
     */
    private $articleClaps;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="user")
     */
    private $articles;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Author", cascade={"persist", "remove"})
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Inbox", mappedBy="user")
     */
    private $inboxes;

    public function __construct()
    {
        $this->pTVComments = new ArrayCollection();
        $this->event = new ArrayCollection();
        $this->announce = new ArrayCollection();
        $this->adverts = new ArrayCollection();
        $this->businesses = new ArrayCollection();
        $this->roles = array('ROLE_USER');
        $this->feedBacks = new ArrayCollection();
        $this->articleClaps = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->inboxes = new ArrayCollection();
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

        if ($this->user_name===null){
            return "";
        }

        return $this->user_name;
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


    public function setUserName(string $user_name): self
    {
        if (!$user_name){
            $this->user_name = $this->email;
        }
        else{
            $this->user_name = $user_name;
        }


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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGsm(): ?string
    {
        return $this->gsm;
    }

    public function setGsm(?string $gsm): self
    {
        $this->gsm = $gsm;

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

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registration_date;
    }

    public function setRegistrationDate(\DateTimeInterface $registration_date): self
    {
        $this->registration_date = $registration_date;

        return $this;
    }

    /**
     * @return Collection|FeedBack[]
     */
    public function getFeedBacks(): Collection
    {
        return $this->feedBacks;
    }

    public function addFeedBack(FeedBack $feedBack): self
    {
        if (!$this->feedBacks->contains($feedBack)) {
            $this->feedBacks[] = $feedBack;
            $feedBack->setUser($this);
        }

        return $this;
    }

    public function removeFeedBack(FeedBack $feedBack): self
    {
        if ($this->feedBacks->contains($feedBack)) {
            $this->feedBacks->removeElement($feedBack);
            // set the owning side to null (unless already changed)
            if ($feedBack->getUser() === $this) {
                $feedBack->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ArticleClaps[]
     */
    public function getArticleClaps(): Collection
    {
        return $this->articleClaps;
    }

    public function addArticleClap(ArticleClaps $articleClap): self
    {
        if (!$this->articleClaps->contains($articleClap)) {
            $this->articleClaps[] = $articleClap;
            $articleClap->setUser($this);
        }

        return $this;
    }

    public function removeArticleClap(ArticleClaps $articleClap): self
    {
        if ($this->articleClaps->contains($articleClap)) {
            $this->articleClaps->removeElement($articleClap);
            // set the owning side to null (unless already changed)
            if ($articleClap->getUser() === $this) {
                $articleClap->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Inbox[]
     */
    public function getInboxes(): Collection
    {
        return $this->inboxes;
    }

    public function addInbox(Inbox $inbox): self
    {
        if (!$this->inboxes->contains($inbox)) {
            $this->inboxes[] = $inbox;
            $inbox->setUser($this);
        }

        return $this;
    }

    public function removeInbox(Inbox $inbox): self
    {
        if ($this->inboxes->contains($inbox)) {
            $this->inboxes->removeElement($inbox);
            // set the owning side to null (unless already changed)
            if ($inbox->getUser() === $this) {
                $inbox->setUser(null);
            }
        }

        return $this;
    }
}
