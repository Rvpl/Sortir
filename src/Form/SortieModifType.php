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

class SortieModifType extends AbstractType
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
                'empty_data' => ''
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                "label" => "Date et heure de la sortie : ",
            ])
            ->add('duree', TimeType::class, [
                "label" => "Durée :",

            ])
            ->add('dateLimiteInscription',DateTimeType::class, [
                "label" => "Date limite d'inscription : ",

            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                "label" => "Nombre de places :",
            ])
            ->add('infoSortie', TextareaType::class, [
                "label" => "Description et informations : ",
                'empty_data' => ''
            ])

            ->add('ville',EntityType::class,[
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'veuillez sélectionner une ville',
            ])
            ->add('cp',TextType::class,[
                'label' => 'Code Postal',
                'disabled' => true,
                'mapped' => false,
                'empty_data' => ''
            ])
            ->add('lieu',EntityType::class,[
                'placeholder' => 'Veuillez choisiri un lieu',
                'class' => Lieu::class,
                'label' => 'Choisir un lieu'
            ])
            ->add('rue',TextType::class,[
                'label' => 'Rue',
                'mapped' => false,
                'empty_data' => '',
                'disabled' => true,
            ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA,
        function (FormEvent $event){
            $data = $event->getData();

             /* @var $ville Ville */
            $ville = $data->getVille();
            $form = $event->getForm();
            if($ville){
                $form->get('ville')->setData($data->getVille());
                $form->get('cp')->setData($data->getVille()->getCodePostal());
                $form->get('lieu')->setData($data->getLieu());
                $form->get('dateHeureDebut')->setData($data->getDateHeureDebut());
                $form->get('dateLimiteInscription')->setData($data->getDateLimiteInscription());
                $form->get('duree')->setData($data->getDuree());
                $form->get('rue')->setData($data->getLieu()->getRue());
            }
        });
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
