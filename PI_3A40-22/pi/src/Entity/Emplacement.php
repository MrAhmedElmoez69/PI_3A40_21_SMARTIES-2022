<?php

namespace App\Entity;

use App\Repository\EmplacementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EmplacementRepository::class)
 */
class Emplacement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="l'emplacement est requis")
     * @Groups ("post:read")
     */
    private $lieu;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(message="capacité doit etre positive")
     * @Assert\NotBlank(message="la capacité du site est requis")
     * @Groups ("post:read")
     */
    private $capacite;

    /**
     * @ORM\ManyToOne(targetEntity=Stock::class, inversedBy="idEmplacement")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Stock;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): self
    {
        $this->capacite = $capacite;

        return $this;
    }



    public function getStock(): ?Stock
    {
        return $this->Stock;
    }

    public function setStock(Stock $Stock): self
    {
        $this->Stock = $Stock;

        return $this;
    }



}
