<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AbonnementRepository::class)
 */
class Abonnement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="type est requis")
     */
    private $type;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan("today")
     */
    private $dated;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan("today")
     * @Assert\Expression(
     *     "this.getdated() < this.getdatef()",
     *     message="La date fin ne doit pas être antérieure à la date début")
     */
    private $datef;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive(message="le prix doit etre positive")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="abonnements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idUser;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDated(): ?\DateTimeInterface
    {
        return $this->dated;
    }

    public function setDated(\DateTimeInterface $dated): self
    {
        $this->dated = $dated;

        return $this;
    }

    public function getDatef(): ?\DateTimeInterface
    {
        return $this->datef;
    }

    public function setDatef(\DateTimeInterface $datef): self
    {
        $this->datef = $datef;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getIdUser(): ?Users
    {
        return $this->idUser;
    }

    public function setIdUser(?Users $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }
    public function __toString() {
        return $this->type;
    }
}
