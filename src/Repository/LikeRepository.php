<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 *
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Like::class);
    }

    public function save(Like $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Like $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function recrutersWhoLikedCandidate(int $likedId): array{
        // On créé le queryBuilder avec l'alias de l'entité concernée
        $queryBuilder = $this->createQueryBuilder('l');

        $queryBuilder->andWhere('l.likedType = :liked') //type du recruteur liké
                     ->andWhere('l.recruter = :id') //id du recruteur connecté
                     ->setParameter('liked', "Candidate")
                     ->setParameter('id', $likedId)
                     ->orderBy('l.date');

        return $queryBuilder->getQuery()->getResult();
    }


    public function candidatesWhoLikedRecruter(int $likedId): array{
        // On créé le queryBuilder avec l'alias de l'entité concernée
        $queryBuilder = $this->createQueryBuilder('l');

        $queryBuilder->andWhere('l.likedType = :liked') //type du candidat liké
                     ->andWhere('l.candidate = :id') //id du candidat connecté
                     ->setParameter('liked', "Recruteur")
                     ->setParameter('id', $likedId)
                     ->orderBy('l.date');

        return $queryBuilder->getQuery()->getResult();
    }

}