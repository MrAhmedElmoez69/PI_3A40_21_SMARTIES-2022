<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('image',FileType::class,array('label'=>'inserer une image','data_class' => null))
            ->add('description')
            ->add('prix')
            ->add('type', ChoiceType::class, array(
                'choices'  => array(
                    ''=>'',
                    'Piece de Rechange' => "Piece de Rechange",
                    'Accessoire' => "Accessoire",
                    'Velo' => "Velo",
                )))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }


    /*

     class EvenementRepository extends \Doctrine\ORM\EntityRepository
{
    public  function findByMot($mot) {
        $query = $this->createQueryBuilder('e')
            ->where('e.titre LIKE :mot')
            ->setParameter('mot', '%'.$mot.'%')
            ->getQuery();

        return $query->getResult();
    }
}
    public function statistique_accessoire()
    {

        $conn = $this->getEntityManager()
            ->getConnection();
        $sql = "SELECT count(*) as nombre,p.Lib_C FROM `accessoire` a INNER JOIN produit p WHERE p.ID_P=a.ID_p GROUP by p.Lib_C";

        try {
            $stmt = $conn->prepare($sql);
        } catch (DBALException $e) {
        }
        $stmt->execute();
        return $stmt->fetchAll();

    }
        public function mise_a_jour()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $sql = "DELETE FROM `achat` WHERE DATEDIFF(DATE_F, NOW()) < 0";

        try {
            $stmt = $conn->prepare($sql);
        } catch (DBALException $e) {
        }
        $stmt->execute();
        return 1;

    }
    public  function triPrix()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM `produit` ORDER BY `produit`.`prix` DESC";
        try {
            $stmt = $conn->prepare($sql);
        } catch (DBALException $e) {
        }
        $stmt->execute();
        return $stmt;
    }
     */
}
