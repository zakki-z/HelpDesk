<?php

namespace App\Form;

use App\Entity\Technicien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechnicienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('specialite', TextType::class, [
                'label' => 'Spécialité',
                'attr' => ['placeholder' => 'Ex: Réseau, Imprimantes, PC...'],
            ])
            ->add('niveau_competence', ChoiceType::class, [
                'label' => 'Niveau de compétence',
                'choices' => [
                    'Junior' => 'junior',
                    'Intermédiaire' => 'intermediaire',
                    'Senior' => 'senior',
                    'Expert' => 'expert',
                ],
            ])
            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Technicien::class,
        ]);
    }

    public function getParent(): string
    {
        return PersonnelType::class;
    }
}
