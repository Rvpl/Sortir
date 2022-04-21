<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\RechercheType;
use App\Form\SortieAnnulType;
use App\Form\SortieModifType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]
    public function index(Request $request,SortieRepository $sortieRepository,CampusRepository $campusRepository,
                          ParticipantRepository $participantRepository,EtatRepository $etatRepository): Response
    {
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
            $ListeSorties = $sortieRepository->findAllFiltre();
            foreach ($ListeSorties as $sortie){
                $date = new \DateTime();
                $dateTimeStamp = $date->getTimestamp();
                $sortieHeureDebut = $sortie->getDateHeureDebut()->getTimestamp()-7200;
                $sortieDuree = $sortie->getDuree()*60;
                $dateLimite = $sortie->getDateLimiteInscription()->getTimestamp()-7200;
                $finSortie = $sortieHeureDebut + $sortieDuree;

                if($dateTimeStamp > $sortieHeureDebut && $dateTimeStamp < $finSortie){
                    $sortie->setEtat($etatRepository->findOneBy(['id' => '3']));
                }
                if($dateTimeStamp > $finSortie){
                    $sortie->setEtat($etatRepository->findOneBy(['id' => '4']));
                }
                if($dateTimeStamp > $dateLimite && $dateTimeStamp < $sortieHeureDebut){
                    $sortie->setEtat($etatRepository->findOneBy(['id' => '6']));
                }
                $sortieRepository->modifEtat($sortie);
            }
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
        $lieu = $lieuRepository->findAll();
        $ville = $villeRepository->findAll();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $this->extracted($form);


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
            'nom' => $sortie->getNom(),
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
                         ParticipantRepository $participantRepository


    ): Response
    {
        $user = $participantRepository->findOneBy(['pseudo' => $this->getUser()->getUserIdentifier()]);
        if ($sortie->getOrganisateur() !== $user){
            throw $this->createAccessDeniedException();
        }
        $lieu = $lieuRepository->findAll();
        $ville = $villeRepository->findAll();
        $form = $this->createForm(SortieModifType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->extracted($form);

            if ($form['dateHeureDebut']->getData() > new \DateTime("now") && $form['dateLimiteInscription']->getData() > new \DateTime("now") && $form['dateHeureDebut']->getData() > $form['dateLimiteInscription']->getData()) {
                $sortie->setLieu($form['lieu']->getData());
                $sortieRepository->add($sortie);
                $this->addFlash('success','La sortie a bien été modifiée');
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
        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
            'villes' => $ville,
            'lieux' => $lieu,

        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
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

        if(!$this->getUser()) {
            $this->addFlash('error','Vous n\'êtes pas connecté pour vous inscrire à une activité');
        }else {
            if ($sortie->getInscrits()->count() === $sortie->getNbInscriptionMax()) {
                $this->addFlash('error', 'Le nombre d\'inscrits maximum est déjà atteint pour cette sortie');
            } else {
                $userVide = $this->getUser()->getUserIdentifier();
                $etat = $etatRepository->findOneBy(['id' => 6]);
                $user = $participantRepository->findOneBy(['pseudo' => $userVide]);
                $nb = 0;
                if($sortie->getInscrits()->count() == 0){
                    $sortieRepository->ajoutInscrit($sortie, $user, $etat);
                }else{
                    foreach ($sortie->getInscrits() as $inscrit) {
                        if ($user->getId() == $inscrit->getId()) {
                            $this->addFlash('error','Vous êtes déjà inscrit à cette activité');
                            break;
                        } else {
                            $nb++;
                            if ($nb === $sortie->getInscrits()->count()) {
                                $sortieRepository->ajoutInscrit($sortie, $user, $etat);
                            }
                        }
                    }
                }
            }
        }
        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('sortie/activite/desister/{id}', name: 'app_sortie_desister', methods: ['POST','GET'])]
    public function desister(Sortie $sortie, ParticipantRepository $participantRepository,EtatRepository $etatRepository, SortieRepository $sortieRepository):Response{

        if($this->getUser() == null) {
            $this->addFlash('error', 'Vous n\'êtes pas connecté pour vous désister à cette sortie');
        }else {
            if($sortie->getInscrits()->count() === 0){
                $this->addFlash('error','La liste des sorties est vide, vous ne pouvez pas vous désinscrire');
            }else{
                $userVide = $this->getUser()->getUserIdentifier();
                $etat = $etatRepository->findOneBy(['id' => 2]);
                $user = $participantRepository->findOneBy(['pseudo' => $userVide]);
                $nb = 0;

                foreach ($sortie->getInscrits() as $inscrit){
                    if($user->getId() === $inscrit->getId()){
                        $sortieRepository->removeInscrit($sortie,$user,$etat);
                        break;
                    }else{
                        $nb++;
                        if($nb === $sortie->getInscrits()->count()){
                            $this->addFlash('error','Vous n\'êtes pas inscrit à cette sortie pour vous désister');
                        }
                    }
                }
            }
        }
        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param FormInterface $form
     * @return void
     */
    public function extracted(FormInterface $form): void
    {
        if ($form['dateHeureDebut']->getData() < new \DateTime("now")) {
            $this->addFlash('error', 'La date de début de l\'activité ne peut être inférieure à la date/heure du jour');
        }
        if ($form['dateLimiteInscription']->getData() < new \DateTime("now")) {
            $this->addFlash('error', 'La date limite d\'inscription ne peut être inférieure à la date/heure du jour');
        }
        if ($form['dateLimiteInscription']->getData() > $form['dateHeureDebut']->getData()) {
            $this->addFlash('error', 'La date limite d\'inscription ne peut être supérieure à la date de début de l\'actitivé');
        }
    }
}
