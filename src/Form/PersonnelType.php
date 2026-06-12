<?php

namespace App\Form;

use App\Entity\Personnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class PersonnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];

        $builder
            ->add('matricule', TextType::class, [
                'label'       => 'Matricule',
                'required'    => true,
                'constraints' => [new NotBlank(message: 'Le matricule est obligatoire.')],
                'attr'        => ['placeholder' => 'Ex: ONCF-001'],
            ])
            ->add('nom', TextType::class, [
                'label'       => 'Nom',
                'required'    => true,
                'constraints' => [new NotBlank(message: 'Le nom est obligatoire.')],
            ])
            ->add('prenom', TextType::class, [
                'label'       => 'Prénom',
                'required'    => true,
                'constraints' => [new NotBlank(message: 'Le prénom est obligatoire.')],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'Email',
                'required'    => true,
                'constraints' => [
                    new NotBlank(message: 'L\'email est obligatoire.'),
                    new Email(message: 'L\'adresse email n\'est pas valide.'),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label'    => $isEdit ? 'Nouveau mot de passe (laisser vide pour garder l\'actuel)' : 'Mot de passe',
                'mapped'   => false,
                'required' => !$isEdit,
                'constraints' => $isEdit ? [] : [
                    new NotBlank(message: 'Le mot de passe est obligatoire.'),
                    new Length(min: 6, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'),
                ],
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add('fonction', TextType::class, [
                'label'       => 'Fonction',
                'required'    => true,
                'constraints' => [new NotBlank(message: 'La fonction est obligatoire.')],
            ])
            ->add('date_embauche', DateType::class, [
                'label'       => 'Date d\'embauche',
                'widget'      => 'single_text',
                'required'    => true,
                'constraints' => [new NotNull(message: 'La date d\'embauche est obligatoire.')],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personnel::class,
            'is_edit'    => false,
        ]);
        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
