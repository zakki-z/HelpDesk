<?php

namespace App\Form;

use App\Entity\Responsable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ResponsableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service', TextType::class, [
                'label'       => 'Service',
                'required'    => true,
                'constraints' => [new NotBlank(message: 'Le service est obligatoire.')],
                'attr'        => ['placeholder' => 'Ex: Direction SI, Maintenance...'],
            ])
            ->add('date_nomination', DateType::class, [
                'label'       => 'Date de nomination',
                'widget'      => 'single_text',
                'required'    => true,
                'constraints' => [new NotNull(message: 'La date de nomination est obligatoire.')],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Responsable::class]);
    }

    public function getParent(): string
    {
        return PersonnelType::class;
    }
}
