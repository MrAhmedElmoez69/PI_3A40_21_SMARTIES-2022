<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ("post:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="Commandes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ("post:read")
     */
    private $idUser;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="Commandes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ("post:read")
     */
    private $idProduit;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="nombre de produits is required")
     * @Assert\Positive
     * @Groups ("post:read")
     */
    private $nbProduits;

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

    public function getIdProduit(): ?Produit
    {
        return $this->idProduit;
    }

    public function setIdProduit(?Produit $idProduit): self
    {
        $this->idProduit = $idProduit;

        return $this;
    }

    public function getNbProduits(): ?int
    {
        return $this->nbProduits;
    }

    public function setNbProduits(int $nbProduits): self
    {
        $this->nbProduits = $nbProduits;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
