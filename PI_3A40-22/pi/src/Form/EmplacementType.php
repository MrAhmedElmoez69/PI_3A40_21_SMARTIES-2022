<?php

namespace App\Form;

use App\Entity\Emplacement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmplacementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Stock')
            ->add('lieu', ChoiceType::class, array(
                'choices'  => array(
                                        'Ariana'=>'Ariana',
                                        'Béja'=>'Béja',
                                        'Ben Arous'=>'Ben Arous',
                                        'Bizerte'=>'Bizerte',
                                        'Gabes'=>'Gabes',
                                        'Gafsa'=>'Gafsa',
                                        'Jendouba'=>'Jendouba',
                                        'Kairouan'=>'Kairouan',
                                        'Kasserine'=>'Kasserine',
                                        'Kebili'=>'Kebili',
                                        'La Manouba'=>'La Manouba',
                                        'Le Kef'=>'Le Kef',
                                        'Mahdia'=>'Mahdia',
                                        'Médenine'=>'Médenine',
                                        'Monastir'=>'Monastir',
                                        'Nabeul'=>'Nabeul',
                                        'Sfax'=>'Sfax',
                                        'Sidi Bouzid'=>'Sidi Bouzid',
                                        'Siliana'=>'Siliana',
                                        'Sousse'=>'Sousse',
                                        'Tataouine'=>'Tataouine',
                                        'Tozeur'=>'Tozeur',
                                        'Tunis'=>'Tunis',
                                        'Zaghouan'=>'Zaghouan',
                )))
            ->add('capacite')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emplacement::class,
        ]);
    }
}
