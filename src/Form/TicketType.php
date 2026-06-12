<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\Equipement;
use App\Entity\Panne;
use App\Entity\SLA;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description du problème',
                'attr'  => ['rows' => 4, 'placeholder' => 'Décrivez le problème en détail...'],
            ])
            ->add('priorite', ChoiceType::class, [
                'label'   => 'Priorité',
                'choices' => array_flip(Ticket::PRIORITES),
            ])
            ->add('equipement', EntityType::class, [
                'class'        => Equipement::class,
                'choice_label' => fn(Equipement $e) => $e->getNom() . ' (' . $e->getType() . ')',
                'label'        => 'Équipement concerné',
                'placeholder'  => '— Aucun équipement —',
                'required'     => false,
            ])
            ->add('panne', EntityType::class, [
                'class'        => Panne::class,
                'choice_label' => fn(Panne $p) => $p->getTypePanne() . ' — ' . $p->getGravite(),
                'label'        => 'Type de panne',
                'placeholder'  => '— Sélectionner —',
                'required'     => false,
            ])
            ->add('sla', EntityType::class, [
                'class'        => SLA::class,
                'choice_label' => fn(SLA $s) => 'Réponse ' . $s->getTempsMaxReponse() . 'h / Résolution ' . $s->getTempsMaxResolution() . 'h',
                'label'        => 'SLA applicable',
                'placeholder'  => '— Aucun SLA —',
                'required'     => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Ticket::class]);
    }
}
