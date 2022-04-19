<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\RechercheType;
use App\Form\RechercheVilleType;
use App\Form\VilleType;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ville')]
#[isGranted('ROLE_ADMIN')]
class VilleController extends AbstractController
{
    #[Route('/', name: 'app_ville_index', methods: ['GET','POST'])]
    public function index(Request $request, VilleRepository $villeRepository,ParticipantRepository $participantRepository): Response
    {
        $villes= [];
        $ville = new Ville();
        $villeRecherche = new Ville();

        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        $formRecherche= $this->createForm(RechercheVilleType::class, $villes);
        $formRecherche->handleRequest($request);



        if ($formRecherche->isSubmitted() && $formRecherche->isValid()){
            $villeRecherche->setNom($formRecherche['ville']->getData());
            $villes = $villeRepository->recherche($villeRecherche);

            return $this->renderForm('ville/index.html.twig',[
                'villes' => $villes,
                'formRecherche'=>$formRecherche,
                'formAjout' => $form
            ]);
        }

        else if ($form->isSubmitted() && $form->isValid()) {
            $villeRepository->add($ville);
            return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);

        }else {
            return $this->renderForm('ville/index.html.twig', [
                'villes' => $villeRepository->findAll(),
                'ville' => $ville,
                'formAjout' => $form,
                'formRecherche'=>$formRecherche
            ]);
        }




    }





    #[Route('/{id}', name: 'app_ville_show', methods: ['GET'])]
    public function show(Ville $ville,LieuRepository $lieuRepository): Response
    {
        return $this->render('ville/show.html.twig', [
            'ville' => $ville,
            'lieux' => $lieuRepository->findByVille($ville)
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ville_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ville $ville, VilleRepository $villeRepository): Response
    {
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $villeRepository->add($ville);
            return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ville/edit.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ville_delete', methods: ['POST'])]
    public function delete(Request $request, Ville $ville, VilleRepository $villeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ville->getId(), $request->request->get('_token'))) {
            $villeRepository->remove($ville);
        }

        return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
    }
}
