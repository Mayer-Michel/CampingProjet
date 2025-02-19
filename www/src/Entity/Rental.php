<?php

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['api'])]
    private ?int $nbrAdult = null;

    #[ORM\Column]
    #[Groups(['api'])]
    private ?int $nbrChildren = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['api'])]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['api'])]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[Groups(['api'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[Groups(['api'])]
    private ?Hebergement $hebergement = null;

    #[ORM\Column]
    #[Groups(['api'])]
    private ?int $prixTotal = null;

    #[ORM\Column(length: 20)]
    private ?string $statu = null;

    #[ORM\Column]
    #[Groups(['api'])]
    private ?bool $clean = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbrAdult(): ?int
    {
        return $this->nbrAdult;
    }

    public function setNbrAdult(int $nbrAdult): static
    {
        $this->nbrAdult = $nbrAdult;

        return $this;
    }

    public function getNbrChildren(): ?int
    {
        return $this->nbrChildren;
    }

    public function setNbrChildren(int $nbrChildren): static
    {
        $this->nbrChildren = $nbrChildren;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getHebergement(): ?Hebergement
    {
        return $this->hebergement;
    }

    public function setHebergement(?Hebergement $hebergement): static
    {
        $this->hebergement = $hebergement;

        return $this;
    }

    public function getPrixTotal(): ?int
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(int $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getStatu(): ?string
    {
        return $this->statu;
    }

    public function setStatu(string $statu): static
    {
        $this->statu = $statu;

        return $this;
    }

    public function isClean(): ?bool
    {
        return $this->clean;
    }

    public function setClean(bool $clean): static
    {
        $this->clean = $clean;

        return $this;
    }
}
