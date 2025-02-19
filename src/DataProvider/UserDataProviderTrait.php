<?php

namespace App\DataProvider;

use App\Entity\NotificationType;
use App\Entity\Payment;
use App\Entity\User;

trait UserDataProviderTrait
{
    protected function getHighlights(
        int $period,
        int $limit,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ) {
        // 1- Le membre qui aura fait le plus de recommandations ces 30 derniers jours
        $queryBuilder = $this->getTheMostRecommanderMemberOverTheLastDays($period, 1);
        $others = $this->getCollectionByQueryBuilder($queryBuilder, $resourceClass, $operationName, $context);

        // 2- Le membre qui aura rencontré le plus d’autres membres ces 30 derniers jours
        $queryBuilder = $this->getTheMostMetMemberOverTheLastDays($period, 1, $others);
        $other2 = $this->getCollectionByQueryBuilder($queryBuilder, $resourceClass, $operationName, $context);
        $others = array_merge($others, $other2);

        // 3- Le membre qui aura été le plus actif ces 30 derniers jours (Recommandations + Rencontres + Participation aux Events)
        $queryBuilder = $this->getTheMostActiveOthersInTheLastDays($period, 1, $others);
        $other3 = $this->getCollectionByQueryBuilder($queryBuilder, $resourceClass, $operationName, $context);
        $others = array_merge($others, $other3);

        // 4 - Ensuite apparaitront les 7 nouveaux membres payants de l’application (ordre chronologique)
        $queryBuilder = $this->getTheNewPayingMembers($limit - count($others), $others);
        $other4 = $this->getCollectionByQueryBuilder($queryBuilder, $resourceClass, $operationName, $context);

        return array_merge($others, $other4);
    }

    /**
     *  1- Les membres qui auront fait le plus de recommandations ces $nbLastDays derniers jours
     */
    public function getTheMostRecommanderMemberOverTheLastDays($nbLastDays, $total, array $exclude = [])
    {
        $manager = $this->managerRegistry->getManagerForClass(User::class);
        $repository = $manager->getRepository(User::class);

        $queryBuilder = $repository->createQueryBuilder('u');

        $queryBuilder->select('u, COUNT(DISTINCT(sn.id)) AS HIDDEN _rank');
        $queryBuilder->leftJoin('u.senderNotifications', 'sn');

        $queryBuilder->andWhere('sn.createdAt BETWEEN DATE_SUB(CURRENT_DATE(), ' . $nbLastDays . ", 'day') AND CURRENT_DATE()");

        $queryBuilder->andWhere('sn.type = :type');
        $queryBuilder->setParameter('type', NotificationType::RECOMMAND_USER);
        if ($exclude) {
            $queryBuilder->andWhere('u.id NOT IN (:user)');
            $queryBuilder->setParameter('user', $exclude);
        }

        $queryBuilder->groupBy('u.id');
        $queryBuilder->orderBy('_rank', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }

    /**
     *  2- Les membres qui auront rencontré le plus d’autres membres ces $nbLastDays derniers jours
     */
    public function getTheMostMetMemberOverTheLastDays($nbLastDays, $total, array $exclude = [])
    {
        $manager = $this->managerRegistry->getManagerForClass(User::class);
        $repository = $manager->getRepository(User::class);

        $queryBuilder = $repository->createQueryBuilder('u');

        $queryBuilder->select('u, COUNT(DISTINCT(r.id)) AS HIDDEN _rank');
        $queryBuilder->leftJoin('u.relationships', 'r');

        $queryBuilder->andWhere("r.createdAt BETWEEN DATE_SUB(CURRENT_DATE(), " . $nbLastDays . ", 'day') AND CURRENT_DATE()");
        if ($exclude) {
            $queryBuilder->andWhere('u.id NOT IN (:user)');
            $queryBuilder->setParameter('user', $exclude);
        }

        $queryBuilder->groupBy('u.id');
        $queryBuilder->orderBy('_rank', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }

    /**
     *  3- Les membres qui auront été les plus actifs ces $nbLastDays derniers jours (Recommandations + Rencontres)
     */
    public function getTheMostActiveMemberInTheLastDays($nbLastDays, $total, array $exclude = [])
    {
        $manager = $this->managerRegistry->getManagerForClass(User::class);
        $repository = $manager->getRepository(User::class);

        $queryBuilder = $repository->createQueryBuilder('u');

        $queryBuilder->select('u, COUNT(DISTINCT(sn.id)) + COUNT(DISTINCT(r.id)) AS HIDDEN _rank');

        $queryBuilder->leftJoin('u.senderNotifications', 'sn');
        $queryBuilder->leftJoin('u.relationships', 'r');

        $queryBuilder->andWhere("sn.createdAt BETWEEN DATE_SUB(CURRENT_DATE(), " . $nbLastDays . ", 'day') AND CURRENT_DATE()");
        $queryBuilder->andWhere("r.createdAt BETWEEN DATE_SUB(CURRENT_DATE(), " . $nbLastDays . ", 'day') AND CURRENT_DATE()");

        $queryBuilder->andWhere('sn.type = :type');
        $queryBuilder->setParameter('type', NotificationType::RECOMMAND_USER);
        if ($exclude) {
            $queryBuilder->andWhere('u.id NOT IN (:user)');
            $queryBuilder->setParameter('user', $exclude);
        }

        $queryBuilder->groupBy('u.id');
        $queryBuilder->orderBy('_rank', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }

    /**
     *  3- Les membres qui ont le plus participé à des events ces $nbLastDays derniers jours
     */
    public function getTheMostEventOthersInTheLastDays($nbLastDays, $total, array $exclude = [])
    {
        $manager = $this->managerRegistry->getManagerForClass(User::class);
        $repository = $manager->getRepository(User::class);

        $queryBuilder = $repository->createQueryBuilder('u');

        $queryBuilder->select('u, COUNT(DISTINCT(ue.id)) AS HIDDEN _rank');

        $queryBuilder->leftJoin('u.userEvents', 'ue');
        $queryBuilder->leftJoin('ue.payment', 'p');

        $queryBuilder->andWhere("ue.registrationDate BETWEEN DATE_SUB(CURRENT_DATE(), " . $nbLastDays . ", 'day') AND CURRENT_DATE()");

        $queryBuilder->andWhere('p.paymentStatus != :status');
        $queryBuilder->setParameter('status', Payment::STATUS_CANCELED);
        if ($exclude) {
            $queryBuilder->andWhere('u.id NOT IN (:user)');
            $queryBuilder->setParameter('user', $exclude);
        }

        $queryBuilder->groupBy('u.id');
        $queryBuilder->orderBy('_rank', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }

    /**
     *  3- Les membres qui auront été les plus actifs ces $nbLastDays derniers jours (Recommandations + Rencontres + Participation aux Events)
     */
    public function getTheMostActiveOthersInTheLastDays($nbLastDays, $total, array $exclude = [])
    {
        $manager = $this->managerRegistry->getManagerForClass(User::class);
        $repository = $manager->getRepository(User::class);

        $queryBuilder = $repository->createQueryBuilder('u');

        $queryBuilder->select('u, COUNT(DISTINCT(sn.id)) + COUNT(DISTINCT(r.id)) + COUNT(DISTINCT(ue.id)) AS HIDDEN _rank');

        $queryBuilder->leftJoin('u.senderNotifications', 'sn');
        $queryBuilder->leftJoin('u.relationships', 'r');
        $queryBuilder->leftJoin('u.userEvents', 'ue');
        $queryBuilder->leftJoin('ue.payment', 'p');

        $queryBuilder->andWhere("sn.createdAt BETWEEN DATE_SUB(CURRENT_DATE(), " . $nbLastDays . ", 'day') AND CURRENT_DATE()");
        $queryBuilder->andWhere("r.createdAt BETWEEN DATE_SUB(CURRENT_DATE(), " . $nbLastDays . ", 'day') AND CURRENT_DATE()");
        $queryBuilder->andWhere("ue.registrationDate BETWEEN DATE_SUB(CURRENT_DATE(), " . $nbLastDays . ", 'day') AND CURRENT_DATE()");

        $queryBuilder->andWhere('sn.type = :type_recommand_user OR sn.type = :type_swap_request');
        $queryBuilder->setParameter('type_recommand_user', NotificationType::RECOMMAND_USER);
        $queryBuilder->setParameter('type_swap_request', NotificationType::SWAAPE_REQUEST);

        $queryBuilder->andWhere('p.paymentStatus != :status');
        $queryBuilder->setParameter('status', Payment::STATUS_CANCELED);
        if ($exclude) {
            $queryBuilder->andWhere('u.id NOT IN (:user)');
            $queryBuilder->setParameter('user', $exclude);
        }

        $queryBuilder->groupBy('u.id');
        $queryBuilder->orderBy('_rank', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }

    /**
     *  4 - Ensuite apparaitront les $total nouveaux membres payants de l’applications (ordre chronologique)
     */
    public function getTheNewPayingMembers($total, array $exclude = [])
    {
        $manager = $this->managerRegistry->getManagerForClass(User::class);
        $repository = $manager->getRepository(User::class);

        $queryBuilder = $repository->createQueryBuilder('u');

        $queryBuilder->select('u, ue.registrationDate AS HIDDEN _rank');
        $queryBuilder->leftJoin('u.userEvents', 'ue');
        $queryBuilder->leftJoin('ue.payment', 'p');

        $queryBuilder->andWhere('p.paymentStatus != :status');
        $queryBuilder->setParameter('status', Payment::STATUS_CANCELED);
        if ($exclude) {
            $queryBuilder->andWhere('u.id NOT IN (:user)');
            $queryBuilder->setParameter('user', $exclude);
        }

        $queryBuilder->groupBy('u.id, _rank');
        $queryBuilder->orderBy('_rank', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }


    public function getNumberUsers()
    {
        $manager = $this->managerRegistry->getManagerForClass(User::class);
        $repository = $manager->getRepository(User::class);
        $queryBuilder = $repository->createQueryBuilder('u');
        $queryBuilder->select('COUNT(u)');

        return $queryBuilder;
    }


    public function getNumberUsersOnline()
    {
        $dateTime = new \DateTime();
        $manager = $this->managerRegistry->getManagerForClass(User::class);
        $repository = $manager->getRepository(User::class);
        $queryBuilder = $repository->createQueryBuilder('u');
        $queryBuilder->select('COUNT(u)');
        $queryBuilder->andWhere('u.updatedAt >=:dateTime');
        $queryBuilder->setParameter('dateTime', $dateTime->modify('-1 hour'));

        return $queryBuilder;
    }

}
