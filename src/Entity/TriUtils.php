<?php

namespace App\Entity;


class TriUtils
{

    private $site;

    private $motRecherche;

    private $dateDebut;

    private $dateFin;

    private $isOrga;

    private $isInscrit;

    private $isNonInscrit;

    private $isPassee;


// ACCESSEURS
    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getMotRecherche(): ?string
    {
        return $this->motRecherche;
    }

    public function setMotRecherche(?string $motRecherche): self
    {
        $this->motRecherche = $motRecherche;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getIsOrga(): ?bool
    {
        return $this->isOrga;
    }

    public function setIsOrga(?bool $isOrga): self
    {
        $this->isOrga = $isOrga;

        return $this;
    }

    public function getIsInscrit(): ?bool
    {
        return $this->isInscrit;
    }

    public function setIsInscrit(?bool $isInscrit): self
    {
        $this->isInscrit = $isInscrit;

        return $this;
    }

    public function getIsNonInscrit(): ?bool
    {
        return $this->isNonInscrit;
    }

    public function setIsNonInscrit(?bool $isNonInscrit): self
    {
        $this->isNonInscrit = $isNonInscrit;

        return $this;
    }

    public function getIsPassee(): ?bool
    {
        return $this->isPassee;
    }

    public function setIsPassee(?bool $isPassee): self
    {
        $this->isPassee = $isPassee;

        return $this;
    }
}
