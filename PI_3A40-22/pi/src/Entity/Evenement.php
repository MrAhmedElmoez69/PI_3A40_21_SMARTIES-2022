<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="nom de l'evenement is required")
     * @Assert\Length(
     *      min = "10",
     *      max = "50",
     *      minMessage = "le nom doit faire au moins {{ limit }} caractères",
     *      maxMessage = "le nom ne peut pas être plus long que {{ limit }} caractères"
     * )
     * @Groups("post:read")
     */
    private $nom;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan("today")
     * @Groups("post:read")
     */
    private $dateD;

    /**
     * @ORM\Column(type="date")
     * @Assert\Expression(
     *     "this.getdateD() < this.getdateF()",
     *     message="La date fin ne doit pas être antérieure à la date début")
     * @Groups("post:read")
     */
    private $dateF;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="lieu is required")
     * @Groups("post:read")
     */
    private $lieu;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="type is required")
     * @Groups("post:read")
     */
    private $type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message="nb participants is required")
     * @Assert\Positive(message="nb participants doit etre positive")
     * @Groups("post:read")
     */
    private $nb_participants;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message="nb_places is required")
     * @Assert\Positive(message="nb places doit etre positive")
     * @Groups("post:read")
     */
    private $nb_places;

    /**
     * @ORM\OneToMany(targetEntity=Activite::class, mappedBy="idEvenement")
     */
    private $activites;

    /**
     * @ORM\OneToMany(targetEntity=Participation::class, mappedBy="IdEvent", orphanRemoval=true)
     */
    private $participations;



    public function __construct()
    {
        $this->activites = new ArrayCollection();
        $this->participations = new ArrayCollection();
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

    public function getDateD(): ?\DateTimeInterface
    {
        return $this->dateD;
    }

    public function setDateD(\DateTimeInterface $dateD): self
    {
        $this->dateD = $dateD;

        return $this;
    }

    public function getDateF(): ?\DateTimeInterface
    {
        return $this->dateF;
    }

    public function setDateF(\DateTimeInterface $dateF): self
    {
        $this->dateF = $dateF;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNbParticipants(): ?int
    {
        return $this->nb_participants;
    }

    public function setNbParticipants(?int $nb_participants): self
    {
        $this->nb_participants = $nb_participants;

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nb_places;
    }

    public function setNbPlaces(?int $nb_places): self
    {
        $this->nb_places = $nb_places;

        return $this;
    }

    /**
     * @return Collection|Activite[]
     */
    public function getActivites(): Array
    {
        return $this->activites->toArray();
    }

    public function addActivite(Activite $activite): self
    {
        if (!$this->activites->contains($activite)) {
            $this->activites[] = $activite;
            $activite->setIdEvenement($this);
        }

        return $this;
    }

    public function removeActivite(Activite $activite): self
    {
        if ($this->activites->removeElement($activite)) {
            // set the owning side to null (unless already changed)
            if ($activite->getIdEvenement() === $this) {
                $activite->setIdEvenement(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->id;
    }

    /**
     * @return Collection|Participation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setIdEvent($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getIdEvent() === $this) {
                $participation->setIdEvent(null);
            }
        }

        return $this;
    }


}
