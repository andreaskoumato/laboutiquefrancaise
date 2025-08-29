<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Votre compte est correctement créé, veuillez vous connecter!"
            );

            return $this->redirectToRoute("app_login");
        }

        $mail = new Mail();
        $vars = [
            'firstname' => $user->getFirstname()
        ];
        $mail->send($user->getEmail(), $user->getFirstName() . " " . $user->getLastname(), "Bienvenue sur la Boutique Française", 'welcome.html', $vars);
        
        return $this->render('register/index.html.twig', [
            'registerForm'=> $form->createView(),
        ]
    );
    }
}
