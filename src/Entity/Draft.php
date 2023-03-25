<?php

namespace App\Entity;

use App\Repository\DraftRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DraftRepository::class)]
class Draft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $brief = null;

    #[ORM\Column]
    private ?int $pageAmount = null;

    #[ORM\OneToOne(inversedBy: 'draft', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?book $book = null;

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

    public function getBrief(): ?string
    {
        return $this->brief;
    }

    public function setBrief(string $brief): self
    {
        $this->brief = $brief;

        return $this;
    }

    public function getPageAmount(): ?int
    {
        return $this->pageAmount;
    }

    public function setPageAmount(int $pageAmount): self
    {
        $this->pageAmount = $pageAmount;

        return $this;
    }

    public function getBook(): ?book
    {
        return $this->book;
    }

    public function setBook(book $book): self
    {
        $this->book = $book;

        return $this;
    }
}
