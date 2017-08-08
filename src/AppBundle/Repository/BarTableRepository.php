<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * BarTableRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BarTableRepository extends EntityRepository
{
    public function findAllTables()
    {
        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select( 't.id', 't.name')
            ->from('AppBundle:BarTable', 't');

        return $dql->getQuery()->getResult();
    }
}
