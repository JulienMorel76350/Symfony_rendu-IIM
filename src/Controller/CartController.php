<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Entity\CartContent;
use App\Entity\Product;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'cart_index', methods: ['GET'])]
    public function index(CartRepository $cartRepository, CartService $cartService): Response
    {
        $tabProducts = [];
        $total = 0;
        $userCart = $cartRepository->findOneBy(['user' => $this->getUser(), 'state' => false]);

        if ($userCart) {
            foreach ($userCart->getCartContents() as $cartContent) {
                $tabProducts[] = [
                    'product' => $cartContent->getProduct(),
                    'quantity' => $cartContent->getQuantity(),
                    'total' => $cartContent->getProduct()->getPrice() * $cartContent->getQuantity()
                ];
                $total += $cartContent->getProduct()->getPrice() * $cartContent->getQuantity();
            }
        } else {
            $userCart = $cartService->create($this->getUser());
        }

        return $this->render('cart/index.html.twig', [
            'userCart' => $userCart,
            'tabProducts' => $tabProducts,
            'total' => $total,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/paid', name: 'cart_paid', methods: ['GET', 'POST'])]
    public function paidCart(EntityManagerInterface $entityManager, CartService $cartService): Response
    {
        $user = $this->getUser();
        $userCart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user, 'state' => false]);

        if (!$userCart || count($userCart->getCartContents()) == 0) {
            $this->addFlash("error", "Vous n'avez pas de panier à payer");
            return $this->redirectToRoute("cart_index");
        }

        foreach ($userCart->getCartContents() as $cartContent) {
            if ($cartContent->getProduct()->getSupply() < $cartContent->getQuantity()) {
                $this->addFlash("error", "Product " . $cartContent->getProduct()->getName() . "n'as plus de stock disponible");
                return $this->redirectToRoute("cart_index");
            }
        }

        $userCart->setState(true);
        $userCart->setDateOfPurchase(new \DateTimeImmutable());
        $entityManager->flush();
        $entityManager->refresh($userCart);

        $cartService->create($user);

        $this->addFlash("success", "You have paid your cart");
        return $this->redirectToRoute("app_homepage");
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/add/{productId}', name: 'cart_add_product', methods: ['GET', 'POST'])]
    public function cartAddProduct(EntityManagerInterface $entityManager, int $productId, CartService $cartService): Response
    {
        $user = $this->getUser();
        $product = $entityManager->getRepository(Product::class)->find($productId);
        $userCart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user, 'state' => false]);

        if (!$product) {
            $this->addFlash("error", "Produit non trouvé");
            return $this->redirectToRoute("app_homepage");
        }

        if (!$userCart) {
            $userCart = $cartService->create($user);
        }

        $alreadyInCart = $entityManager->getRepository(CartContent::class)->findOneBy(['cart' => $userCart, 'product' => $product]);

        if ($alreadyInCart) {
            $alreadyInCart->setQuantity($alreadyInCart->getQuantity() + 1);
            $this->addFlash("success", "Vous avez " . $alreadyInCart->getQuantity() . " " . $product->getName() . " dans votre panier");
        } else {
            $cartContent = new CartContent();
            $cartContent->setCart($userCart);
            $cartContent->setProduct($product);
            $cartContent->setQuantity(1);
            $cartContent->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($cartContent);
            $this->addFlash("success", "Vous avez ajouté " . $product->getName() . " dans votre panier");
        }

        $entityManager->flush();
        $entityManager->refresh($userCart);


        return $this->redirectToRoute('app_homepage', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remove-one/{idProduct}', name: 'cart_remove_one_product', methods: ['GET', 'POST'])]
    public function removeOne(EntityManagerInterface $entityManager, $idProduct): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($idProduct);
        $user = $this->getUser();
        $globalCart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user, 'state' => false]);

        if (!$product) {
            throw $this->createNotFoundException('Pas de produit trouvé pour id : ' . $idProduct);
            $this->addFlash("error", "Pas de produit trouvé pour id");
        }

        $cartContent = $entityManager->getRepository(CartContent::class)->findOneBy(['product' => $product, 'cart' => $globalCart]);

        if (!$cartContent) {
            $this->addFlash("error", "Le produit n'est pas dans votre panier");
        } else {
            $globalCart->removeCartContent($cartContent);
            $entityManager->remove($cartContent);
            $entityManager->flush();

            $this->addFlash("success", "Le produit à été retiré de votre panier");
        }

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/remove/{id}', name: 'cart_remove_product', methods: ['GET', 'POST'])]
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $cartToDelete = $entityManager->getRepository(Cart::class)->find($id);

        if (!$cartToDelete) {
            throw $this->createNotFoundException('Aucun panier trouvé pour ce produit ' . $id);
            return $this->redirectToRoute("app_homepage");
        }

        $entityManager->remove($cartToDelete);
        $entityManager->flush();
        $this->addFlash("success", "Le panier à été vidé");
        return $this->redirectToRoute("app_homepage");
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/order/{id}', name: 'get_my_order', methods: ['GET', 'POST'])]
    public function getMyOrder(EntityManagerInterface $entityManager, $id): Response
    {
        $user = $this->getUser();
        $userCart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user, 'state' => true, 'id' => $id]);
        $products = $entityManager->getRepository(CartContent::class)->findBy(['cart' => $userCart]);

        return $this->render('cart/show.html.twig', [
            'cart' => $userCart,
            'products' => $products
        ]);
    }
}
