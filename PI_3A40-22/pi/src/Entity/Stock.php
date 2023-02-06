<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=StockRepository::class)
 */
class Stock
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
     * @Assert\NotBlank(message="le champs de libelle de stock est requis")
     * @Groups ("post:read")
     */
    private $libelle;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="le champs de prix est requis")
     * @Assert\Positive(message="prix du stock doit etre positive")
     * @Groups ("post:read")
     */
    private $prix;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="le champs quantite de stock est requis")
     * @Assert\LessThan(
     *     value = 5000
     * )
     * @Assert\GreaterThan(
     *     value = 5
     * )
     * @Assert\PositiveOrZero(message="quantite du stock doit etre positive")
     * @Groups ("post:read")
     */
    private $quantite;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="le champs de disponibilité est requis")
     * @Assert\Choice(choices = {"Disponible", "Non Disponible"}, message = "Choisire disponibilite soit 'Disponible' soit 'Non Disponible'." )
     * @Groups ("post:read")
     */
    private $disponibilite;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="Stock")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idProduit;

    /**
     * @ORM\ManyToOne(targetEntity=Emplacement::class, inversedBy="Emplacement")
     * @ORM\JoinColumn(nullable=true)
     */
    private $emplacement;


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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(string $disponibilite): self
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }
    public function getIdProduit(): ?Produit
    {
        return $this->idProduit;
    }

    public function setIdProduit(?Produit $produit): self
    {
        $this->idProduit = $produit;

        return $this;
    }

    public function getEmplacement(): ?Emplacement
    {
        return $this->emplacement;
    }

    public function setEmplacement(Emplacement $emplacement): self
    {
        // set the owning side of the relation if necessary
        if ($emplacement->getStock() !== $this) {
            $emplacement->setStock($this);
        }

        $this->emplacement = $emplacement;

        return $this;
    }
    public function __toString() {
        return $this->libelle;
    }

    public function __toString1() {
        return $this->disponibilite;
    }

}
