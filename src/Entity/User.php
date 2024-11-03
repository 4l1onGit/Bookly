<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    /**
     * @var Collection<int, BookReview>
     */
    #[ORM\ManyToMany(targetEntity: BookReview::class, inversedBy: 'users')]
    private Collection $reviews;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    /**
     * @var Collection<int, BookReview>
     */
    #[ORM\OneToMany(targetEntity: BookReview::class, mappedBy: 'reviewer', orphanRemoval: true)]
    private Collection $bookReviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->bookReviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, BookReview>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(BookReview $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
        }

        return $this;
    }

    public function removeReview(BookReview $review): static
    {
        $this->reviews->removeElement($review);

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, BookReview>
     */
    public function getBookReviews(): Collection
    {
        return $this->bookReviews;
    }

    public function addBookReview(BookReview $bookReview): static
    {
        if (!$this->bookReviews->contains($bookReview)) {
            $this->bookReviews->add($bookReview);
            $bookReview->setReviewer($this);
        }

        return $this;
    }

    public function removeBookReview(BookReview $bookReview): static
    {
        if ($this->bookReviews->removeElement($bookReview)) {
            // set the owning side to null (unless already changed)
            if ($bookReview->getReviewer() === $this) {
                $bookReview->setReviewer(null);
            }
        }

        return $this;
    }
}
