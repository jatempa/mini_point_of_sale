<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    public function findAllCategories()
    {
        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('c.id', 'c.name')
            ->from('AppBundle:Category', 'c');

        return $dql->getQuery()->getResult();
    }
}
