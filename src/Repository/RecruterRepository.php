<?php

namespace App\Repository;

use App\Entity\Recruter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recruter>
 *
 * @method Recruter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recruter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recruter[]    findAll()
 * @method Recruter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecruterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recruter::class);
    }

    public function save(Recruter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recruter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
