<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
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
     * @Assert\NotBlank(message="le champs de libelle du produit est requis")
     * @Groups ("post:read")
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="L'image du produit est requis")
     * @Groups ("post:read")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="le champs de la description du produit est requis")
     * @Groups ("post:read")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Stock::class, mappedBy="idProduit")
     */
    private $Stock;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="idProduit")
     */
    private $Commandes;

    /**
     * @ORM\OneToMany(targetEntity=Achat::class, mappedBy="idProduit")
     */
    private $achats;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="le champs de prix du produit est requis")
     * @Assert\Positive(message="le champs de prix doit etre positive")
     * @Groups ("post:read")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ("post:read")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Favoris::class, mappedBy="IdProduit", orphanRemoval=true)
     */
    private $favoris;

    public function __construct()
    {
        $this->Commandes = new ArrayCollection();
        $this->achats = new ArrayCollection();
        $this->favoris = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->Commandes;
    }

    public function addCommande(Commande $Commande): self
    {
        if (!$this->Commandes->contains($Commande)) {
            $this->Commandes[] = $Commande;
            $Commande->setIdProduit($this);
        }

        return $this;
    }

    public function removeCommande(Commande $Commande): self
    {
        if ($this->Commandes->removeElement($Commande)) {
            // set the owning side to null (unless already changed)
            if ($Commande->getIdProduit() === $this) {
                $Commande->setIdProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Achat[]
     */
    public function getAchats(): Collection
    {
        return $this->achats;
    }

    public function addAchat(Achat $achat): self
    {
        if (!$this->achats->contains($achat)) {
            $this->achats[] = $achat;
            $achat->setIdProduit($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getIdProduit() === $this) {
                $achat->setIdProduit(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->libelle;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function setPrix($prix): self
    {
        $this->prix = $prix;

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

    /**
     * @return Collection|Favoris[]
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Favoris $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
            $favori->setIdProduit($this);
        }

        return $this;
    }

    public function removeFavori(Favoris $favori): self
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getIdProduit() === $this) {
                $favori->setIdProduit(null);
            }
        }

        return $this;
    }


}
