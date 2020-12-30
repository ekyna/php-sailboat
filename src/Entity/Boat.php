<?php

namespace App\Entity;

use App\Repository\BoatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BoatRepository::class)
 * @UniqueEntity("name")
 */
class Boat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity=Position::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $position;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setPosition(new Position());
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

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): self
    {
        if ($position) {
            $position->setBoat($this);
        }

        $this->position = $position;

        return $this;
    }
}
