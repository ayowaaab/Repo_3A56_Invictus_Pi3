<?php

namespace App\Repository;

use App\Entity\Droit;
use App\Entity\Radiologist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Droit>
 *
 * @method Droit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Droit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Droit[]    findAll()
 * @method Droit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DroitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Droit::class);
    }

//    /**
//     * @return Droit[] Returns an array of Droit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Droit
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findRadioloquesWithoutAccessToImage($imageId)
    {
        $qb = $this->createQueryBuilder('d');
        $qb->leftJoin('d.radioloqist', 'r')
            ->leftJoin('d.image', 'i')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('i.id'),
                $qb->expr()->notIn('d.role', ['guest', 'owner'])
            ))
            ->setParameter('imageId', $imageId); // This line is incorrect

        return $qb->getQuery()->getResult();
    }
    public function findRadioloqueWithoutDroit($imageId)
    {
        // Supposons que $entityManager est votre instance de EntityManagerInterface
        $qb = $this->_em->createQueryBuilder();

        $qb->select('r')
            ->from('App\Entity\Radiologist', 'r')
            ->leftJoin('App\Entity\Droit', 'd', 'WITH', 'd.radioloqist = r.id AND d.image = :imageId')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('d.id'),
                $qb->expr()->neq('d.role', ':role'),
            ))

            ->setParameter('imageId', $imageId)
            ->setParameter('role', 'guest')
        ;
        return $qb->getQuery()->getResult();
    }

}
