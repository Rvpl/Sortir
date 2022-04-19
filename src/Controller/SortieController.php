<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\RechercheType;
use App\Form\SortieAnnulType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]
    public function index(Request $request,SortieRepository $sortieRepository,CampusRepository $campusRepository,
                          ParticipantRepository $participantRepository,VilleRepository $villeRepository,LieuRepository $lieuRepository): Response
    {
        $sorties = [];
        $campus = new Campus();
        $sortieCherche = new Sortie();
        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campus = $campusRepository->findBy(['id'=> $form['campus']->getData()]);
            $sortieCherche->setNom($form['nom']->getData());
            $sortieCherche->setCampus($campus[0]);
            $sortieCherche->setDateHeureDebut($form['dateHeureDebut']->getData());
            $sortieCherche->setDateLimiteInscription($form['dateLimiteInscription']->getData());
            $sorties = $sortieRepository->recherche($sortieCherche);


            return $this->renderForm('sortie/index.html.twig',[
                'formRecherche' => $form,
                'sorties' => $sorties,
                'participants' => $participantRepository->findAll(),
            ]);
        }else{

            return $this->renderForm('sortie/index.html.twig', [
                'sorties' => $sortieRepository->findAllFiltre(),
                'formRecherche' => $form,
                'participants' => $participantRepository->findAll(),
            ]);
        }
    }

    #[Route('sortie/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request,
                        SortieRepository $sortieRepository,
                        VilleRepository $villeRepository,
                        LieuRepository $lieuRepository,
                        EtatRepository $etatRepository,
                        ParticipantRepository $participantRepository,

    ): Response
    {

        $sortie = new Sortie();
        $user = $participantRepository->findOneBy(['pseudo' => $this->getUser()->getUserIdentifier()]);
        $sortie->setCampus($user->getCampus());
        $form = $this->createForm(SortieType::class,$sortie);
        $lieu = $lieuRepository->findAll();
        $ville = $villeRepository->findAll();
         $form = $this->createForm(SortieType::class);
        $user = $participantRepository->findOneBy(['pseudo' => $this->getUser()->getUserIdentifier()]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $messages = array();
            if ($form['dateHeureDebut']->getData() < new \DateTime("now")) {
                $this->addFlash('error','La date de début de l\'activité ne peut être inférieure à la date/heure du jour');
            }
            if ($form['dateLimiteInscription']->getData() < new \DateTime("now")) {
                $this->addFlash('error','La date limite d\'inscription ne peut être inférieure à la date/heure du jour');
            }
            if ($form['dateLimiteInscription']->getData() > $form['dateHeureDebut']->getData()) {
                $this->addFlash('error','La date limite d\'inscription ne peut être supérieure à la date de début de l\'actitivé');
            }


            if ($form['dateHeureDebut']->getData() > new \DateTime("now") && $form['dateLimiteInscription']->getData() > new \DateTime("now") && $form['dateHeureDebut']->getData() > $form['dateLimiteInscription']->getData()) {
                $sortie->setEtat($etatRepository->findAll()[0]);
                $sortie->setOrganisateur($this->getUser());
                $sortie->setCampus($user->getCampus());
                $sortie->setLieu($form['lieu']->getData());
                $sortieRepository->add($sortie);
                $this->addFlash('success','La sortie a bien été créée');
                return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->renderForm('sortie/new.html.twig', [
                    'sortie' => $sortie,
                    'form' => $form,
                    'villes' => $ville,
                    'lieux' => $lieu,
                ]);
            }
        }
        return $this->renderForm('sortie/new.html.twig',[
            'sortie' => $sortie,
            'form' => $form,
            'villes' => $ville,
            'lieux' => $lieu,

        ]);
    }

    #[Route('sortie/{id}', name: 'app_sortie_show', methods: ['GET', 'POST'])]
    public function show(Sortie $sortie, VilleRepository $villeRepository, SortieRepository $sortieRepository,
                         Request $request, EtatRepository $etatRepository,
                         LieuRepository $lieuRepository,ParticipantRepository $participantRepository): Response
    {
        $user = $participantRepository->findOneBy(['pseudo' => $this->getUser()->getUserIdentifier()]);
        if ($sortie->getOrganisateur() !== $user){
            throw $this->createAccessDeniedException();
        }

        $formAnnul = $this->createForm(SortieAnnulType::class, $sortie);
        $formAnnul->handleRequest($request);
        if ($formAnnul->isSubmitted() && $formAnnul->isValid()) {
            $etat = $etatRepository->findOneBy(['id' => '5']);
            $sortieRepository->modifEtatAnn($sortie, $etat);
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }
        $lieu = $lieuRepository->findAll();
        $sorties = $sortieRepository->findOneBy(['id' => $sortie->getId()]);
        $ville = $villeRepository->findAll();
        return $this->renderForm('sortie/show.html.twig', [
            'sortie' => $sortie,
            'villes' => $ville,
            'sorties' => $sorties,
            'formAnnul'=>$formAnnul,
            'lieux' => $lieu,
        ]);
    }

    #[Route('sortie/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie,
                         SortieRepository $sortieRepository,
                         VilleRepository $villeRepository,
                         LieuRepository $lieuRepository,
                         ParticipantRepository $participantRepository,
    ): Response
    {
        $user = $participantRepository->findOneBy(['pseudo' => $this->getUser()->getUserIdentifier()]);
        if ($sortie->getOrganisateur() !== $user){
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie);
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }
        $lieu = $lieuRepository->findAll();
        $ville = $villeRepository->findAll();
        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
            'villes' => $ville,
            'lieux' => $lieu,

        ]);
    }

    #[Route('sortie/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('sortie/etat/{id}', name: 'app_modif_etat', methods: ['GET'])]
    public function modifEtat(Sortie $sortie, SortieRepository $sortieRepository,EtatRepository $etatRepository): Response
    {
       $sortie->setEtat($etatRepository->findOneBy(['id' => '2']));
       $sortieRepository->modifEtat($sortie);
       return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('sortie/activite/inscription/{id}', name: 'app_sortie_inscription', methods: ['POST','GET'])]
    public function inscription(Sortie $sortie, ParticipantRepository $participantRepository,EtatRepository $etatRepository, SortieRepository $sortieRepository):Response{
        $userVide = $this->getUser()->getUserIdentifier();
        $etat = $etatRepository->findOneBy(['id' => 6]);
        $user = $participantRepository->findOneBy(['pseudo' => $userVide]);
        $sortieRepository->ajoutInscrit($sortie,$user,$etat);
        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('sortie/activite/desister/{id}', name: 'app_sortie_desister', methods: ['POST','GET'])]
    public function desister(Sortie $sortie, ParticipantRepository $participantRepository,EtatRepository $etatRepository, SortieRepository $sortieRepository):Response{
        $userVide = $this->getUser()->getUserIdentifier();
        $etat = $etatRepository->findOneBy(['id' => 2]);
        $user = $participantRepository->findOneBy(['pseudo' => $userVide]);
        $sortieRepository->removeInscrit($sortie,$user,$etat);
        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }
}
