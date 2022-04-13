<?php

namespace App\Form;

use App\Entity\Ville;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheVilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('ville',\Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label'=>'Le nom contient :'])
        ;
    }



}
