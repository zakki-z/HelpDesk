<?php

namespace App\Form;

use App\Entity\SLA;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SLAType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('temps_max_reponse', IntegerType::class, [
                'label' => 'Temps max de réponse (heures)',
                'attr'  => ['min' => 1, 'placeholder' => 'Ex: 4'],
            ])
            ->add('temps_max_resolution', IntegerType::class, [
                'label' => 'Temps max de résolution (heures)',
                'attr'  => ['min' => 1, 'placeholder' => 'Ex: 24'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SLA::class]);
    }
}
