<?php

namespace App\Form;

use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_article', TextType::class, [
                'label' => 'Nom de l\'article',
                'attr'  => ['placeholder' => 'Ex: Câble réseau RJ45, Cartouche HP...'],
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité en stock',
                'attr'  => ['min' => 0],
            ])
            ->add('seuil_min', IntegerType::class, [
                'label' => 'Seuil d\'alerte minimum',
                'attr'  => ['min' => 0],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Stock::class]);
    }
}
