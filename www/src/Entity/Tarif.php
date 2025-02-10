<?php

namespace App\Entity;

use App\Repository\TarifRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TarifRepository::class)]
class Tarif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Saison $saisonId = null;

    #[ORM\Column]
    private ?float $prix = null;

    /**
     * @var Collection<int, Hebergement>
     */
    #[ORM\OneToMany(targetEntity: Hebergement::class, mappedBy: 'tarifId')]
    private Collection $hebergement;

    #[ORM\ManyToOne(inversedBy: 'tarif')]
    private ?Hebergement $hebergementId = null;

    public function __construct()
    {
        $this->hebergement = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaisonId(): ?Saison
    {
        return $this->saisonId;
    }

    public function setSaisonId(?Saison $saisonId): static
    {
        $this->saisonId = $saisonId;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Hebergement>
     */
    public function getHebergement(): Collection
    {
        return $this->hebergement;
    }

    public function addHebergement(Hebergement $hebergement): static
    {
        if (!$this->hebergement->contains($hebergement)) {
            $this->hebergement->add($hebergement);
            $hebergement->setTarifId($this);
        }

        return $this;
    }

    public function removeHebergement(Hebergement $hebergement): static
    {
        if ($this->hebergement->removeElement($hebergement)) {
            // set the owning side to null (unless already changed)
            if ($hebergement->getTarifId() === $this) {
                $hebergement->setTarifId(null);
            }
        }

        return $this;
    }

    public function getHebergementId(): ?Hebergement
    {
        return $this->hebergementId;
    }

    public function setHebergementId(?Hebergement $hebergementId): static
    {
        $this->hebergementId = $hebergementId;

        return $this;
    }
}
