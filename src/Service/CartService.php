<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{

  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function create(User $user)
  {
    $cart = new Cart();
    $cart->setUser($user);
    $cart->setState(false);
    $this->entityManager->persist($cart);
    $this->entityManager->flush();

    return $cart;
  }

  public function getCartQuantity(User $user): int
  {
    $cart = $this->entityManager->getRepository(Cart::class)->findOneBy(['user' => $user, 'state' => false]);

    if (!$cart) {
      return 0;
    }

    $quantity = 0;
    foreach ($cart->getCartContents() as $cartContent) {
      $quantity += $cartContent->getQuantity();
    }

    return $quantity;
  }
}
