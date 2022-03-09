<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length (max={255}, maxMessage="Le nom ne peut pas faire plus de 255 caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Assert\GreaterThan("Today", message="Vous ne pouvez pas créer de sortie dans le passé!!!")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Range(min="30", max="2880", minMessage="La sortie ne peut durer moins de 30 minutes",
     *     maxMessage="La sortie ne peut durer plus de 2 jours (2880 minutes)")
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Assert\LessThanOrEqual(propertyPath="dateHeureDebut",
     *     message="La date limite d'inscription ne peut être après le début de la sortie")
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Range (min="2", minMessage="Une sortie se fait à plusieurs!!!")
     * @TODO : Voir pour max?
     */
    private $nbInsciptionsMax;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Assert\Length (max={5000}, maxMessage="Pas plus de  5000 caractères")
     */
    private $infosSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $lieu;

    /**
     * @ORM\ManyToMany(targetEntity=Utilisateur::class, mappedBy="participationSortie")
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="organisationSortie")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank ()
     */
    private $organisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motif;


    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->dateHeureDebut = new \DateTime();
        $this->dateLimiteInscription = new \DateTime();
//        if($this->participants->count() >= $this->getNbInsciptionsMax() || $this->dateLimiteInscription <= new \DateTime()){
//            $etatRepository = EtatRepository::class;
//            $etat = $etatRepository->
////            $this->setEtat("");
//        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInsciptionsMax(): ?int
    {
        return $this->nbInsciptionsMax;
    }

    public function setNbInsciptionsMax(int $nbInsciptionsMax): self
    {
        $this->nbInsciptionsMax = $nbInsciptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * @return Collection|Utilisateur[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Utilisateur $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->addParticipationSortie($this);
        }

        return $this;
    }


    public function removeParticipant(Utilisateur $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeParticipationSortie($this);
        }

        return $this;
    }

    public function getOrganisateur(): ?Utilisateur
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Utilisateur $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

}
