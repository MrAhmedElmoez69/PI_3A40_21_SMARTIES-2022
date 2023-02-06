<?php

namespace App\Entity;

use App\Repository\SujetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SujetRepository::class)
 */
class Sujet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Groups ("post:read")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\JoinColumn(nullable=true)
     * @Groups ("post:read")
     *
     */
    private $nbReponses;

    /**
     * @ORM\Column(type="integer")
     * @ORM\JoinColumn(nullable=true)
     * @Groups ("post:read")
     */
    private $nbVues;



    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="sujets")
     * @Groups ("post:read")
     */
    private $idUser;

    /**
     * @ORM\Column(type="date")
     * @Groups ("post:read")
     */
    private $Date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ("post:read")
     * @Assert\NotBlank(message="le titre is required")
     * @Assert\Length(
     *      min = "5",
     *      max = "15",
     *      minMessage = "le titre doit faire au moins {{ limit }} caractères",
     *      maxMessage = "le titre ne peut pas être plus long que {{ limit }} caractères"
     * )
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = "20",
     *      max = "200",
     *      minMessage = "le contenu doit faire au moins {{ limit }} caractères",
     *      maxMessage = "le contenu ne peut pas être plus long que {{ limit }} caractères"
     * )
     * @Assert\NotBlank(message="le contenu is required")
     * @Groups ("post:read")
     */
    private $contenu;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="idSujet")
     * @Groups ("post:read")
     */
    private $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getnbReponses(): ?int
    {
        return $this->nbReponses;
    }

    public function setnbReponses(int $nbReponses): self
    {
        $this->nbReponses = $nbReponses;

        return $this;
    }

    public function getnbVues(): ?int
    {
        return $this->nbVues;
    }

    public function setnbVues(int $nbVues): self
    {
        $this->nbVues = $nbVues;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(?\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

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

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Array
    {
        return $this->messages->toArray();
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setIdSujet($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getIdSujet() === $this) {
                $message->setIdSujet(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return (string) $this->id;
    }
}
