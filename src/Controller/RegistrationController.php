<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Security\ParticipantAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, ParticipantAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() != null){
            $this->addFlash('error','Vous êtes déjà connectez, vous ne pouvez pas créer de nouveau compte sans vous déconnecter');
           return $this->redirectToRoute('app_sortie_index');
        }else{
            $user = new Participant();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setActif(0);
                $user->setAdministrateur(0);

                $image = $form->get('photoProfil')->getData();
                if($image){
                    $fichier = md5(uniqid()). '.'.$image->guessExtension();
                    $image->move(
                        $this->getParameter('images_directory'),
                        $fichier
                    );
                    $user->setPhotoProfil($fichier);
                }

                // encode the plain password
                if($form['plainPassword']->getData() === $form['confirmation']->getData()){
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
                }else{
                    $this->addFlash('error','Les mots de passe ne sont pas identiques');
                    return $this->render('registration/register.html.twig', [
                        'registrationForm' => $form->createView(),
                    ]);
                }


                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );

            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }

    }
}
