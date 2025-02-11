<?php

namespace App\Entity;

use App\Entity\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\HebergementRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: HebergementRepository::class)]
class Hebergement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'hebergement')]
    private ?Type $typeId = null;


    /**
     * @var Collection<int, Equipement>
     */
    #[ORM\ManyToMany(targetEntity: Equipement::class, inversedBy: 'hebergement')]
    private Collection $equipementID;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'hebergement')]
    private Collection $imageId;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?int $surface = null;

    #[ORM\Column]
    private ?bool $disponibilite = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @var Collection<int, Rental>
     */
    #[ORM\OneToMany(targetEntity: Rental::class, mappedBy: 'hebergement')]
    private Collection $rentals;


    public function __construct()
    {
        $this->equipementID = new ArrayCollection();
        $this->imageId = new ArrayCollection();
        $this->rentals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeId(): ?Type
    {
        return $this->typeId;
    }

    public function setTypeId(?Type $typeId): static
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * @return Collection<int, Equipement>
     */
    public function getEquipementID(): Collection
    {
        return $this->equipementID;
    }

    public function addEquipementID(Equipement $equipementID): static
    {
        if (!$this->equipementID->contains($equipementID)) {
            $this->equipementID->add($equipementID);
        }

        return $this;
    }

    public function removeEquipementID(Equipement $equipementID): static
    {
        $this->equipementID->removeElement($equipementID);

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImageId(): Collection
    {
        return $this->imageId;
    }

    public function addImageId(Image $imageId): static
    {
        if (!$this->imageId->contains($imageId)) {
            $this->imageId->add($imageId);
            $imageId->setHebergement($this);
        }

        return $this;
    }

    public function removeImageId(Image $imageId): static
    {
        if ($this->imageId->removeElement($imageId)) {
            // set the owning side to null (unless already changed)
            if ($imageId->getHebergement() === $this) {
                $imageId->setHebergement(null);
            }
        }

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(int $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): static
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals->add($rental);
            $rental->setHebergement($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getHebergement() === $this) {
                $rental->setHebergement(null);
            }
        }

        return $this;
    }
}
