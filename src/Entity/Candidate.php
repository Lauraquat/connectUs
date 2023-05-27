<?php

namespace App\Entity;

use App\Repository\CandidateRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
class Candidate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column]
    private ?int $postalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $searchedPost = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $disponibility = null;

    #[ORM\Column(type: 'string')]
    private ?string $CV = null;


    #[ORM\OneToOne(inversedBy: 'candidate', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: Like::class)]
    private Collection $likes;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getSearchedPost(): ?string
    {
        return $this->searchedPost;
    }

    public function setSearchedPost(string $searchedPost): self
    {
        $this->searchedPost = $searchedPost;

        return $this;
    }

    public function getDisponibility(): ?DateTimeInterface
    {
        return $this->disponibility;
    }

    public function setDisponibility(DateTimeInterface $disponibility): self
    {
        $this->disponibility = $disponibility;

        return $this;
    }

    public function getCV(): string
    {
        return $this->CV;
    }

    public function setCV(string $CV): self
    {
        $this->CV = $CV;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setCandidate($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getCandidate() === $this) {
                $like->setCandidate(null);
            }
        }

        return $this;
    }
}
