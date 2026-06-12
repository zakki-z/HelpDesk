<?php

namespace App\Form;

use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_article', TextType::class, [
                'label'       => 'Nom de l\'article',
                'required'    => true,
                'constraints' => [new NotBlank(message: 'Le nom de l\'article est obligatoire.')],
                'attr'        => ['placeholder' => 'Ex: Câble réseau RJ45, Cartouche HP...'],
            ])
            ->add('quantite', IntegerType::class, [
                'label'       => 'Quantité en stock',
                'required'    => true,
                'constraints' => [
                    new NotNull(message: 'La quantité est obligatoire.'),
                    new GreaterThanOrEqual(value: 0, message: 'La quantité ne peut pas être négative.'),
                ],
                'attr' => ['min' => 0],
            ])
            ->add('seuil_min', IntegerType::class, [
                'label'       => 'Seuil d\'alerte minimum',
                'required'    => true,
                'constraints' => [
                    new NotNull(message: 'Le seuil minimum est obligatoire.'),
                    new GreaterThanOrEqual(value: 0, message: 'Le seuil ne peut pas être négatif.'),
                ],
                'attr' => ['min' => 0],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Stock::class]);
    }
}
