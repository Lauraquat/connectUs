<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'likedByCandidate')]
    private ?Candidate $candidate = null;

    #[ORM\ManyToOne(inversedBy: 'likedByRecruter')]
    private ?Recruter $recruter = null;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }

    public function getRecruter(): ?Recruter
    {
        return $this->recruter;
    }

    public function setRecruter(?Recruter $recruter): self
    {
        $this->recruter = $recruter;

        return $this;
    }
}
