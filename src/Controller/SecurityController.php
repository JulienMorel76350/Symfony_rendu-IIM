<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\UserEditForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: 'auth/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: 'auth/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: 'auth/profil', name: 'app_profil'), IsGranted('ROLE_USER')]
    public function profil(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $userCarts = $em->getRepository(Cart::class)->findBy(['user' => $user, 'state' => true]);;

        return $this->render('user/profil.html.twig', [
            'user' => $user,
            'userCarts' => $userCarts,
        ]);
    }

    #[Route(path: 'auth/profil/update', name: 'app_profil_update'), IsGranted('ROLE_USER')]
    public function updateProfil(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserEditForm::class, $this->getUser());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour');
            return $this->redirectToRoute('app_profil', ['user' => $this->getUser()]);
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Erreur lors de la mise à jour du profil');
        }

        return $this->render('user/profil.html.twig', [
            'form' => $form,
            'user' => $this->getUser(),
        ]);
    }
}
