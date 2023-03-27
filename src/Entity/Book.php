<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
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

    #[ORM\ManyToMany(targetEntity: Person::class, mappedBy: 'book')]
    private Collection $people;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastSaveDate = null;

    #[ORM\OneToOne]
    private ?Book $book = null;

    #[ORM\Column]
    private ?bool $isDraft = false;

    public function __construct()
    {
        $this->people = new ArrayCollection();
    }

    public static function cloneOf(self $book, bool $clearRelations = false): Book
    {
        $cloneBook = new self();
        $book->copyTo($cloneBook, $clearRelations);

        return $cloneBook;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function copyTo(self $book, bool $clearRelations = false): void
    {
        if ($clearRelations) {
            /** @var Person $person */
            foreach ($this->people as $person) {
                $person->removeBook($this);
            }
        }

        $book
            ->setName($this->getName())
            ->setPageAmount($this->getPageAmount())
            ->setBrief($this->getBrief())
            ->setIsDraft($this->isDraft())
            ->setOriginalBook($this->getOriginalBook())
            ->setPeople($this->getPeople());
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

    /**
     * @return string
     */
    public function getPeopleNames(): string
    {
        return trim($this->people->reduce(
            function (string $names, Person $person) {
                return $names . "{$person->getName()}, ";
            }, ''
        ), ', ');
    }

    /**
     * @param Collection $people
     */
    public function setPeople(Collection $people): void
    {
        $this->people = $people;

        /** @var Person $person */
        foreach ($people as $person) {
            $person->addBook($this);
        }
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people->add($person);
            $person->addBook($this);
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->removeElement($person)) {
            $person->removeBook($this);
        }

        return $this;
    }

    public function getLastSaveDate(): ?\DateTimeInterface
    {
        return $this->lastSaveDate;
    }

    public function setLastSaveDate(?\DateTimeInterface $lastSaveDate): self
    {
        $this->lastSaveDate = $lastSaveDate;

        return $this;
    }

    public function getOriginalBook(): ?Book
    {
        return $this->book;
    }

    public function setOriginalBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function isDraft(): ?bool
    {
        return $this->isDraft;
    }

    public function setIsDraft(bool $isDraft): self
    {
        $this->isDraft = $isDraft;

        return $this;
    }
}
