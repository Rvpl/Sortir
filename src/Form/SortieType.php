<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                "label" => "Nom de la sortie : "
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                "label" => "Date et heure de la sortie : "
                ])
            ->add('duree', TimeType::class, [
                "label" => "Durée :"
            ])
            ->add('dateLimiteInscription',DateTimeType::class, [
                "label" => "Date limite d'inscription : "
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                "label" => "Nombre de places :"
            ])
            ->add('infoSortie', TextareaType::class, [
                "label" => "Description et informations : "
            ])

            ->add('campus', EntityType::class,[
                "class" => Campus::class,
                "choice_label" => "nom"
            ])

            ->add('lieu', EntityType::class,
                [
                    "class" => Lieu::class,
                    "choice_label" => "nom",
                ])
            ->add('rue', FormType::class, [
                'mapped' => false
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
