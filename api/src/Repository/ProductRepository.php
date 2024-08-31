<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $product)
    {
        try {
            $this->getEntityManager()->persist($product);
            $this->getEntityManager()->flush();
            return new JsonResponse('succes', 201);
        }catch (Exception $e)
        {
            return new JsonResponse('error', 500);
        }
    }

    public function delete(Product $product)
    {
        try {
            $this->getEntityManager()->remove($product);
            $this->getEntityManager()->flush();
            return new JsonResponse('succes', 201);
        }catch (Exception $e)
        {
            return new JsonResponse('error', 500);
        }
    }

    public function deleteProductById(int $id)
    {
        try {
            $product = $this->getEntityManager()->getRepository(Product::class)->find($id);
            $this->getEntityManager()->remove($product);
            $this->getEntityManager()->flush();
            return new JsonResponse('succes', 201);
        }catch (Exception $e)
        {
            return new JsonResponse('error', 500);
        }
    }

    public function toArray(Product $product)
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),

            'price' => $product->getPrice(),
            'stock' => $product->getStock(),
        ];
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
