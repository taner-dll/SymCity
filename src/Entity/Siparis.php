<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiparisRepository")
 */
class Siparis
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
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SiparisDetail", mappedBy="head", orphanRemoval=true)
     */
    private $detail;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $phone;

    /**
     * @ORM\Column(type="datetime")
     */
    private $order_date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $invoice_address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attention_to;

    /**
     * @ORM\Column(type="smallint")
     */
    private $proforma_sent;

    public function __construct()
    {
        $this->detail = new ArrayCollection();
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
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

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $Siparis_code): self
    {
        $this->code = $Siparis_code;

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

    /**
     * @return Collection|SiparisDetail[]
     */
    public function getDetail(): Collection
    {
        return $this->detail;
    }

    public function addDetail(SiparisDetail $detail): self
    {
        if (!$this->detail->contains($detail)) {
            $this->detail[] = $detail;
            $detail->setHead($this);
        }

        return $this;
    }

    public function removeDetail(SiparisDetail $detail): self
    {
        if ($this->detail->contains($detail)) {
            $this->detail->removeElement($detail);
            // set the owning side to null (unless already changed)
            if ($detail->getHead() === $this) {
                $detail->setHead(null);
            }
        }

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->order_date;
    }

    public function setOrderDate(\DateTimeInterface $order_date): self
    {
        $this->order_date = $order_date;

        return $this;
    }

    public function getInvoiceAddress(): ?string
    {
        return $this->invoice_address;
    }

    public function setInvoiceAddress(?string $invoice_address): self
    {
        $this->invoice_address = $invoice_address;

        return $this;
    }

    public function getAttentionTo(): ?string
    {
        return $this->attention_to;
    }

    public function setAttentionTo(?string $attention_to): self
    {
        $this->attention_to = $attention_to;

        return $this;
    }

    public function getProformaSent(): ?int
    {
        return $this->proforma_sent;
    }

    public function setProformaSent(int $proforma_sent): self
    {
        $this->proforma_sent = $proforma_sent;

        return $this;
    }
}
