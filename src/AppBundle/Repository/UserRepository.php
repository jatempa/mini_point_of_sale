<?php
/**
 * Created by PhpStorm.
 * User: atempa
 * Date: 9/08/17
 * Time: 06:30 PM
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAllWaiters()
    {
        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('u.name', 'u.firstLastName', 'u.secondLastName', 'u.username', 'u.cellphoneNumber', 'u.roles')
            ->from('AppBundle:User', 'u')
            ->where('u.roles like \'%MESERO%\'');

        return $dql->getQuery()->getResult();
    }
}