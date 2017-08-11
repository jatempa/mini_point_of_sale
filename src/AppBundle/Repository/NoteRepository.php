<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * NoteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NoteRepository extends EntityRepository
{
    public function findLastNoteIdByUser($user_id)
    {
        $tempNow = new \DateTime('now');

        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('n.numberNote')
            ->from('AppBundle:Note', 'n')
            ->where('n.user = :userId')
            ->andWhere('n.checkin <= :tempNow')
            ->orderBy('n.checkin', 'DESC')
            ->setMaxResults(1);

        $dql->setParameter('tempNow', $tempNow);
        $dql->setParameter('userId', $user_id);

        return $dql->getQuery()->getResult();
    }

    public function findAllPendingNotes()
    {
        $tempNow = new \DateTime('now');

        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('u.id as userId', 'concat(u.name, \' \', u.firstLastName) as waiter', 'n.numberNote', 'n.status', 'p.id as productId', 'p.name as product', 'c.name as category', 'sum(np.amount) as amount')
            ->from('AppBundle:NoteProduct', 'np')
            ->innerJoin('np.product', 'p')
            ->innerJoin('p.category', 'c')
            ->innerJoin('np.note', 'n')
            ->innerJoin('n.user', 'u')
            ->where('n.checkin <= :tempNow')
            ->andWhere('n.status = \'Pendiente\'')
            ->groupBy('u.id', 'n.numberNote', 'p.id')
            ->setMaxResults(20);

        $dql->setParameter('tempNow', $tempNow);

        return $dql->getQuery()->getResult();
    }


    public function findOneNote($userId, $numberNote)
    {
        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('n')
            ->from('AppBundle:Note', 'n')
            ->innerJoin('n.user', 'u')
            ->where('u.id = :userId')
            ->andWhere('n.numberNote = :numberNote');

        $dql->setParameter('userId', $userId);
        $dql->setParameter('numberNote', $numberNote);

        return $dql->getQuery()->getSingleResult();
    }
}
