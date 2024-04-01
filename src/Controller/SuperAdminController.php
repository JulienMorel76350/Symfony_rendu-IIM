<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SuperAdminController extends AbstractController
{
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/admin/users', name: 'admin_get_all_users', methods: ['GET'])]
    public function getAllUsers(EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $entityManager->getRepository(User::class)->findBy([], ['createdAt' => 'DESC']),
        ]);
    }


    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/admin/unpaid', name: 'admin_cart_unpaid', methods: ['GET', 'POST'])]
    public function getUnpaidCarts(CartRepository $cartRepository): Response
    {
        $carts = $cartRepository->findUnpaidCartsWithDetails();
        
        return $this->render('admin/unpaid-order.html.twig', [
            'carts' => $carts
        ]);
    }
}
