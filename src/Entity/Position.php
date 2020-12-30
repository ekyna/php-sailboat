<?php

namespace App\Entity;

use App\Repository\PositionRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PositionRepository::class)
 */
class Position
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Boat::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $boat;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $longitude;

    /**
     * @ORM\Column(type="integer")
     */
    private $latitude;

    /**
     * Angle (degrees).
     *
     * @ORM\Column(type="smallint")
     */
    private $direction;

    /**
     * Speed (meter/second).
     *
     * @ORM\Column(type="smallint")
     */
    private $speed;


    public function __construct()
    {
        $this->date = new DateTime();
        $this->longitude = 0;
        $this->latitude = 0;
        $this->direction = 0;
        $this->speed = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBoat(): ?Boat
    {
        return $this->boat;
    }

    public function setBoat(Boat $boat): self
    {
        $this->boat = $boat;

        return $this;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLongitude(): int
    {
        return $this->longitude;
    }

    public function setLongitude(int $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): int
    {
        return $this->latitude;
    }

    public function setLatitude(int $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    public function setDirection(int $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getSpeed(): int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }
}
