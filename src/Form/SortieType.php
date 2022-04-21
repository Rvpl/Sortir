<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SortieType extends AbstractType
{
    private $security;
    private $participantRepository;
    private $user;

    public function __construct(Security $security,ParticipantRepository $participantRepository)
    {
        $this->security = $security;
        $this->participantRepository=$participantRepository;
        $this->user = $this->participantRepository->findOneBy(["pseudo" => $this->security->getUser()->getUserIdentifier()]);

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                "label" => "Nom de la sortie : ",
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                "label" => "Date et heure de la sortie : ",
                "widget" => "single_text"
            ])
            ->add('duree', IntegerType::class, [
                "label" => "Durée de l'événement :",
            ])
            ->add('dateLimiteInscription',DateTimeType::class, [
                "label" => "Date limite d'inscription : ",
                'data' => new \DateTime(),
                "widget" => "single_text"
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                "label" => "Nombre de places : ",
                "attr"=>['placeholder' => 'Nombre de participants maximum']
            ])
            ->add('infoSortie', TextareaType::class, [
                "label" => "Description et informations : ",
                "attr"=>['placeholder' => 'Description de la sortie...'],
                'label_attr' => ['class' => 'form-descrip']

            ])

            ->add('ville',EntityType::class,[
                'class' => Ville::class,
                'choice_label' => 'nom',
                "label"=>"Ville :",
                'placeholder' => 'Veuillez sélectionner une ville',
            ])
            ->add('cp',TextType::class,[
                'label' => 'Code Postal :',
                "attr"=>['placeholder' => "Code postal de la ville choisi"],
                'disabled' => true,
                'mapped' => false
            ])
            ->add('lieu',EntityType::class,[
                'placeholder' => 'Veuillez sélectionner un lieu',
                'class' => Lieu::class,
               'label' => 'Choisir un lieu :'
            ])
            ->add('rue',TextType::class,[
                'label' => 'Rue :',
                "attr"=>['placeholder' => 'Nom de la rue...'],
                'mapped' => false
            ]);

        $builder->get('ville')->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $this->addCp($form->getParent(),$form->getData());
                $this->addLieuField($form->getParent(),$form->getData());
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }

    private function addCp(FormInterface $form, Ville $ville){
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder('cp',TextType::class,null,
            [
                'data' => $ville->getCodePostal(),
                'mapped' => false,
                'auto_initialize' => false,
                'label' => 'Code Postal',
                'disabled' =>true,
            ]);

        $form->add($builder->getForm());
    }

    private function addLieuField(FormInterface $form,Ville $ville){
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder('lieu',EntityType::class,null,
            [
                'class' => Lieu::class,
                'placeholder' => 'veuillez sélectionner un lieu',
                'choices' => $ville->getLieu(),
                'required' => false,
                'mapped' => false,
                'auto_initialize' => false,
            ]);
        $builder->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event){
                $form = $event->getForm();
                if(!$form->getData()){
                    $lieu = new Lieu($form->getData());

                }else{
                    $lieu = $form->getData();
                }
                $this->addRueField($form->getParent(),$lieu);
            });


        $form->add($builder->getForm());
    }

    private function addRueField(FormInterface $form, Lieu $lieu){
        $form->add('rue',TextType::class,[
            'label' => 'Rue',
            'data' => $lieu->getRue(),
            'mapped' => false,
            'auto_initialize' => false,
            'disabled' => true,
        ]);
    }
}
