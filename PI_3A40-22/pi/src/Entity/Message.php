<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ("post:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ("post:read")
     */
    private $idUser;

    /**
     * @ORM\Column(type="date")
     * @Groups ("post:read")
     */
    private $Date;

    /**
     * @ORM\ManyToOne(targetEntity=Sujet::class, inversedBy="messages")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\JoinColumn(nullable=true)
     */
    private $idSujet;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="le contenu de votre message is required")
     * @Groups ("post:read")
     * @Assert\Length(
     *      min = "5",
     *      max = "50",
     *      minMessage = "le contenu doit faire au moins {{ limit }} caractères",
     *      maxMessage = "le contenu ne peut pas être plus long que {{ limit }} caractères"
     * )
     */
    private $contenu;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getIdSujet(): ?Sujet
    {
        return $this->idSujet;
    }

    public function setIdSujet(?Sujet $idSujet): self
    {
        $this->idSujet = $idSujet;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }
}
