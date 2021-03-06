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
        $dql->select('u.id as userId', 'concat(u.name, \' \', u.firstLastName) as fullname', 'n.numberNote','n.status')
            ->from('AppBundle:NoteProduct', 'np')
            ->innerJoin('np.note', 'n')
            ->innerJoin('n.user', 'u')
            ->where('n.checkin <= :tempNow')
            ->andWhere('n.status = \'Pendiente\'')
            ->andWhere('u.roles like \'%MESERO%\'')
            ->groupBy('u.id', 'n.numberNote', 'n.checkin')
            ->orderBy('n.checkin')
            ->setMaxResults(50);

        $dql->setParameter('tempNow', $tempNow);

        return $dql->getQuery()->getResult();
    }

    public function findUsersWithPendingNotesByDate()
    {
        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('u.id as userId', 'concat(u.name, \' \', u.firstLastName) as fullname', 'n.numberNote','n.status', 'a.id as account')
            ->from('AppBundle:NoteProduct', 'np')
            ->innerJoin('np.note', 'n')
            ->innerJoin('n.account', 'a')
            ->innerJoin('a.user', 'u')
            ->where('n.checkin >= :initialDate')
            ->andWhere('n.status = \'Pendiente\'')
            ->andWhere('u.roles like \'%MESERO%\'')
            ->groupBy('u.id', 'n.numberNote', 'n.checkin')
            ->orderBy('n.checkin')
            ->setMaxResults(50);

        $dql->setParameter('initialDate', new \DateTime('-12 hours'));

        return $dql->getQuery()->getResult();
    }

    public function findUsersWithDeliveredNotes()
    {
        $tempNow = new \DateTime('-36 hours');

        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('u.id as userId', 'concat(u.name, \' \', u.firstLastName) as fullname', 'n.numberNote','n.status', 'n.checkin', 'a.id as account')
            ->from('AppBundle:NoteProduct', 'np')
            ->innerJoin('np.note', 'n')
            ->innerJoin('n.account', 'a')
            ->innerJoin('a.user', 'u')
            ->where('n.checkin >= :tempNow')
            ->andWhere('n.status = \'Entregado\'')
            ->andWhere('u.roles like \'%MESERO%\'')
            ->groupBy('u.id', 'n.numberNote', 'n.checkin')
            ->orderBy('n.checkin', 'DESC');

        $dql->setParameter('tempNow', $tempNow);

        return $dql->getQuery()->getResult();
    }


    public function findProductsByNote($userId, $numberNote)
    {
        $em = $this->getEntityManager();
        $dql = $em->createQueryBuilder();
        $dql->select('p.id', 'p.name as product', 'p.price', 'sum(np.amount) as amount', 'c.name as category')
            ->from('AppBundle:NoteProduct', 'np')
            ->innerJoin('np.product', 'p')
            ->innerJoin('p.category', 'c')
            ->innerJoin('np.note', 'n')
            ->innerJoin('n.user', 'u')
            ->where('u.id = :userId')
            ->andWhere('n.numberNote = :folio')
            ->groupBy('p.id');

        $dql->setParameter('userId', $userId);
        $dql->setParameter('folio', $numberNote);

        return $dql->getQuery()->getResult();
    }
}
