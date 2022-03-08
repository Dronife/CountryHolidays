<?php

namespace App\Repository;

use App\Entity\Holiday;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Holiday|null find($id, $lockMode = null, $lockVersion = null)
 * @method Holiday|null findOneBy(array $criteria, array $orderBy = null)
 * @method Holiday[]    findAll()
 * @method Holiday[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Holiday::class);
    }

    public function findOneOrCreate(Holiday $holiday) : Holiday
    {
        $entity = $this->findOneBy([
            'name' => $holiday->getName(),
            'type' => $holiday->getType(),
            'date' => $holiday->getDate(),
        ]);

        if($entity === null)
        {
            $entity = $this->create($holiday);
        }
        return $entity;
    }

    public function create(Holiday $holiday) : Holiday
    {
        $this->_em->persist($holiday);
        $this->_em->flush();
        return $holiday;
    }

    public function getHolidaysByYearAndCountryName($year, $countryName){
        return $this->createQueryBuilder('h')
            ->leftJoin('h.countries', 'c')
            ->andWhere('YEAR(h.date) = :year')
            ->andWhere('c.name = :countryName')
            ->setParameters(['year' => $year, 'countryName' => $countryName])
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return Holiday[] Returns an array of Holiday objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Holiday
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
