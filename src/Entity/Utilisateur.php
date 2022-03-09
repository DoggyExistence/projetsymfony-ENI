<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @UniqueEntity(fields={"email"})
 * @UniqueEntity(fields={"pseudo"})
 */
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
	 * @Assert\Email()
	 * @Assert\NotBlank()
	 * @Assert\Length(max={50}, maxMessage="Adresse mail limitée à {{ limit }} caractères")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
	 * @Assert\NotBlank
	 * @Assert\Length (min=5, minMessage="Le mot de passe doit avoir au moins {{ limit }} caractères")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank
	 * @Assert\Length (max={255}, maxMessage="Sérieusement ? Le nom doit avoir au maximum {{ limit }} caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank
	 * @Assert\Length (max={255}, maxMessage="Sérieusement ? Le prénom doit avoir au maximum {{ limit }} caractères")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=20)
	 * @Assert\NotBlank
	 * @Assert\Length (max={20}, maxMessage="Le pseudo doit avoir au maximum {{ limit }} caractères")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
	 * @Assert\Length (max={20}, maxMessage="Le numéro de téléphone est limité à {{ limit }} caractères")
     */
    private $telephone;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, inversedBy="participants")
     */
    private $participationSortie;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur", orphanRemoval=true)
     */
    private $organisationSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="utilisateurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

	private $plainPassword;

	/**
	 * @return mixed
	 */
	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

	/**
	 * @param mixed $plainPassword
	 */
	public function setPlainPassword($plainPassword): void
	{
		$this->plainPassword = $plainPassword;
	}

    public function __construct()
    {
        $this->participationSortie = new ArrayCollection();
        $this->organisationSortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getParticipationSortie(): Collection
    {
        return $this->participationSortie;
    }

    public function addParticipationSortie(Sortie $participationSortie): self
    {
        if (!$this->participationSortie->contains($participationSortie)) {
            $this->participationSortie[] = $participationSortie;
        }

        return $this;
    }

    public function removeParticipationSortie(Sortie $participationSortie): self
    {
        $this->participationSortie->removeElement($participationSortie);

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getOrganisationSortie(): Collection
    {
        return $this->organisationSortie;
    }

    public function addOrganisationSortie(Sortie $organisationSortie): self
    {
        if (!$this->organisationSortie->contains($organisationSortie)) {
            $this->organisationSortie[] = $organisationSortie;
            $organisationSortie->setOrganisateur($this);
        }

        return $this;
    }

    public function removeOrganisationSortie(Sortie $organisationSortie): self
    {
        if ($this->organisationSortie->removeElement($organisationSortie)) {
            // set the owning side to null (unless already changed)
            if ($organisationSortie->getOrganisateur() === $this) {
                $organisationSortie->setOrganisateur(null);
            }
        }

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
