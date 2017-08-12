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

    public function findUsersWithPendingNotes()
    {
        $tempNow = new \DateTime('now');

        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('u.id as userId', 'concat(u.name, \' \', u.firstLastName) as fullname, n.numberNote, np.status')
            ->from('AppBundle:NoteProduct', 'np')
            ->innerJoin('np.note', 'n')
            ->innerJoin('n.user', 'u')
            ->where('n.checkin <= :tempNow')
            ->andWhere('np.status = \'Pendiente\'')
            ->groupBy('u.id', 'n.numberNote', 'n.checkin')
            ->orderBy('n.checkin')
            ->setMaxResults(20);

        $dql->setParameter('tempNow', $tempNow);

        return $dql->getQuery()->getResult();
    }

    public function findPendingNoteProducts($userId, $numberNote)
    {
        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('p.id', 'p.name as product', 'sum(np.amount) as amount')
            ->from('AppBundle:NoteProduct', 'np')
            ->innerJoin('np.product', 'p')
            ->innerJoin('np.note', 'n')
            ->innerJoin('n.user', 'u')
            ->where('u.id = :userId')
            ->andWhere('n.numberNote = :folio')
            ->groupBy('p.id');

        $dql->setParameter('userId', $userId);
        $dql->setParameter('folio', $numberNote);

        return $dql->getQuery()->getResult();
    }

    public function findAllDeliveredNotes()
    {
        $tempNow = new \DateTime('now');

        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('u.id as userId', 'concat(u.name, \' \', u.firstLastName) as waiter', 'n.id as noteId', 'np.id as noteProductId', 'n.numberNote', 'np.status', 'p.id as productId', 'p.name as product', 'c.name as category', 'np.amount')
            ->from('AppBundle:NoteProduct', 'np')
            ->innerJoin('np.product', 'p')
            ->innerJoin('p.category', 'c')
            ->innerJoin('np.note', 'n')
            ->innerJoin('n.user', 'u')
            ->where('n.checkin <= :tempNow')
            ->andWhere('np.status = \'Entregado\'')
            ->orderBy('n.checkin', 'desc')
            ->setMaxResults(50);

        $dql->setParameter('tempNow', $tempNow);

        return $dql->getQuery()->getResult();
    }
}
