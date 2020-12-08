<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
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
    private $author_name;

    /**
     * @ORM\Column(type="text")
     */
    private $article;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $confirm;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_views;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleClaps", mappedBy="article", orphanRemoval=true)
     */
    private $articleClaps;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_claps;

    public function __construct()
    {
        $this->articleClaps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthorName(): ?string
    {
        return $this->author_name;
    }

    public function setAuthorName(string $author_name): self
    {
        $this->author_name = $author_name;

        return $this;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function setArticle(string $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getConfirm(): ?int
    {
        return $this->confirm;
    }

    public function setConfirm(int $confirm): self
    {
        $this->confirm = $confirm;

        return $this;
    }

    public function getTotalViews(): ?int
    {
        return $this->total_views;
    }

    public function setTotalViews(int $total_views): self
    {
        $this->total_views = $total_views;

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
            $articleClap->setArticle($this);
        }

        return $this;
    }

    public function removeArticleClap(ArticleClaps $articleClap): self
    {
        if ($this->articleClaps->contains($articleClap)) {
            $this->articleClaps->removeElement($articleClap);
            // set the owning side to null (unless already changed)
            if ($articleClap->getArticle() === $this) {
                $articleClap->setArticle(null);
            }
        }

        return $this;
    }

    public function getTotalClaps(): ?int
    {
        return $this->total_claps;
    }

    public function setTotalClaps(int $total_claps): self
    {
        $this->total_claps = $total_claps;

        return $this;
    }
}
