<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SettingsRepository")
 */
class Settings
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $eur_usd;

    /**
     * @ORM\Column(type="smallint")
     */
    private $renkli_artis_orani;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEurUsd(): ?float
    {
        return $this->eur_usd;
    }

    public function setEurUsd(float $eur_usd): self
    {
        $this->eur_usd = $eur_usd;

        return $this;
    }

    public function getRenkliArtisOrani(): ?int
    {
        return $this->renkli_artis_orani;
    }

    public function setRenkliArtisOrani(int $renkli_artis_orani): self
    {
        $this->renkli_artis_orani = $renkli_artis_orani;

        return $this;
    }
}
