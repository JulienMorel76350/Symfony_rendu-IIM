<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 *
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }
    public function findUnpaidCartsWithDetails()
    {
        // Récupère d'abord les identifiants de paniers qui ont des contenus.
        $qb = $this->createQueryBuilder('cart');
        $qb->select('IDENTITY(cartContents.cart)')
            ->leftJoin('cart.cartContents', 'cartContents')
            ->where('cart.state = :state')
            ->setParameter('state', false)
            ->groupBy('cartContents.cart');

        $cartIdsWithContents = $qb->getQuery()->getScalarResult();

        // Convertit le résultat en un tableau simple d'identifiants.
        $cartIds = array_map(function ($item) {
            return $item[1];
        }, $cartIdsWithContents);

        if (empty($cartIds)) {
            // Si aucun panier n'a de contenu, retourne un tableau vide.
            return [];
        }

        // Utilise les identifiants récupérés pour obtenir les paniers et leurs contenus.
        return $this->createQueryBuilder('cart')
            ->leftJoin('cart.cartContents', 'cartContents')
            ->addSelect('cartContents')
            ->where('cart.id IN (:cartIds)')
            ->setParameter('cartIds', $cartIds)
            ->orderBy('cart.dateOfPurchase', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Cart[] Returns an array of Cart objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Cart
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
