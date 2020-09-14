<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 */
class Player
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"player", "team"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @Groups({"player", "team"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"player", "team"})
     */
    private $last_name;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"player", "team"})
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity=Position::class, inversedBy="players", cascade={"persist"})
     * @Groups({"player", "team"})
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", nullable=false)
     */
    private $positions;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="players", cascade={"persist"})
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false)
     * @Groups({"player"})
     */
    private $team;

    public function __construct()
    {
        $this->positions = new ArrayCollection();
    }

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

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * @return Collection|Position[]
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    public function addPosition(Position $position): self
    {
        if (!$this->positions->contains($position)) {
            $this->positions[] = $position;
        }

        return $this;
    }

    public function removePosition(Position $position): self
    {
        if ($this->positions->contains($position)) {
            $this->positions->removeElement($position);
        }

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
