<?php

namespace App\Entity;

use App\Repository\FavorisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FavorisRepository::class)
 */
class Favoris
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ("post:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="favoris")
     * @ORM\JoinColumn(nullable=True)
     * @Groups ("post:read")
     */
    private $IdUser;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="favoris")
     * @ORM\JoinColumn(nullable=True)
     * @Groups ("post:read")
     */
    private $IdProduit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?Users
    {
        return $this->IdUser;
    }

    public function setIdUser(?Users $IdUser): self
    {
        $this->IdUser = $IdUser;

        return $this;
    }

    public function getIdProduit(): ?Produit
    {
        return $this->IdProduit;
    }

    public function setIdProduit(?Produit $IdProduit): self
    {
        $this->IdProduit = $IdProduit;

        return $this;
    }
}
