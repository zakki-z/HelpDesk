<?php

namespace App\Form;

use App\Entity\Intervention;
use App\Entity\Technicien;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('technicien', EntityType::class, [
                'class'        => Technicien::class,
                'choice_label' => fn(Technicien $t) => $t->getNomComplet() . ' — ' . $t->getSpecialite(),
                'label'        => 'Technicien',
                'placeholder'  => '— Sélectionner —',
            ])
            ->add('commentaire', TextareaType::class, [
                'label'    => 'Commentaire initial',
                'required' => false,
                'attr'     => ['rows' => 3, 'placeholder' => 'Description de l\'intervention...'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Intervention::class]);
    }
}
