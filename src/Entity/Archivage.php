<?php

namespace App\Entity;

use App\Repository\ArchivageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArchivageRepository::class)
 */
class Archivage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateDernierArchivage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDernierArchivage(): ?\DateTimeInterface
    {
        return $this->dateDernierArchivage;
    }

    public function setDateDernierArchivage(?\DateTimeInterface $dateDernierArchivage): self
    {
        $this->dateDernierArchivage = $dateDernierArchivage;

        return $this;
    }
}
