<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Form\RechercherCampusType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Doctrine\DBAL\Types\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/campus')]
class CampusController extends AbstractController
{
    #[Route('/', name: 'app_campus_index', methods: ['GET','POST'])]
    public function index(Request $request,CampusRepository $campusRepository, ParticipantRepository $participantRepository): Response
    {
        $campuses=[];
        $campus = new Campus();
        $campusRecherche= new Campus();
        $userVide = $this->getUser()->getUserIdentifier();
        $user = $participantRepository->findOneBy(['pseudo' => $userVide]);
        $role=$user->getRole();
        $formCampus = $this->createForm(CampusType::class, $campus);
        $formCampus->handleRequest($request);
        $formRecherche= $this->createForm(RechercherCampusType::class, $campuses);
        $formRecherche->handleRequest($request);

        foreach ($role as $roles){
          if($role!=['ROLES_ADMIN']){
              return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);

          }
    }


         if ($formRecherche->isSubmitted() && $formRecherche->isValid()){
            $campusRecherche->setNom($formRecherche['campus']->getData());
            $campuses = $campusRepository->recherche($campusRecherche);

            return $this->renderForm('campus/index.html.twig',[
                'campuses' => $campuses,
                'formRecherche'=>$formRecherche,
                'formAjout' => $formCampus
            ]);

        } else if ($formCampus->isSubmitted() && $formCampus->isValid()) {
            $campusRepository->add($campus);
            return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campus/index.html.twig', [
            'campuses' => $campusRepository->findAll(),
            'campus' => $campus,
            'formAjout' => $formCampus,
            'formRecherche'=>$formRecherche
        ]);


    }



    #[Route('/{id}', name: 'app_campus_show', methods: ['GET'])]
    public function show(Campus $campus): Response
    {
        return $this->render('campus/show.html.twig', [
            'campus' => $campus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_campus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campus $campus, CampusRepository $campusRepository): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campusRepository->add($campus);
            return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campus/edit.html.twig', [
            'campus' => $campus,
            'formCampus' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campus_delete', methods: ['POST'])]
    public function delete(Request $request, Campus $campus, CampusRepository $campusRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campus->getId(), $request->request->get('_token'))) {
            $campusRepository->remove($campus);
        }

        return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
    }
}
