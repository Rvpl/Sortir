<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\RechercheType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]
    public function index(Request $request,SortieRepository $sortieRepository,CampusRepository $campusRepository): Response
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

            ]);
        }else{

            return $this->renderForm('sortie/index.html.twig', [
                'sorties' => $sortieRepository->findAll(),
                'formRecherche' => $form,
            ]);
        }
    }

    #[Route('sortie/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request,
                        SortieRepository $sortieRepository,
                        VilleRepository $villeRepository,
                        LieuRepository $lieuRepository
    ): Response
    {
        $sortie = new Sortie();
        $lieu = $lieuRepository->findAll();
        $ville = $villeRepository->findAll();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setEtat(1);
            $sortie->setOrganisateur(1);
            $sortie->setCampus(1);
            $sortieRepository->add($sortie);
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig',[
            'sortie' => $sortie,
            'form' => $form,
            'villes' => $ville,
            'lieux' => $lieu
        ]);
    }

    #[Route('sortie/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie, VilleRepository $villeRepository,): Response
    {
        $ville = $villeRepository->findAll();
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'villes' => $ville
        ]);
    }

    #[Route('sortie/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie);
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
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
}
