<?php

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use App\Entity\Network;

trait NetworkDataProviderTrait
{

    /**
     *  1- Le réseau qui compte le plus de membres de l’application ET de leur réseau (au national).
     */
    public function getTheMostMembersAndOfflineMembersNetwork($total, array $exclude = [])
    {
        $manager = $this->managerRegistry->getManagerForClass(Network::class);
        $repository = $manager->getRepository(Network::class);

        $queryBuilder = $repository->createQueryBuilder('n');
        $queryBuilder->select("n, COUNT(DISTINCT(nm.id)) + n.nbMembersOffline AS HIDDEN _rank");
        $queryBuilder->leftJoin('n.networkMembers', 'nm');
        if ($exclude) {
            $queryBuilder->andWhere('n.id NOT IN (:network)');
            $queryBuilder->setParameter('network', $exclude);
        }

        $queryBuilder->groupBy('n.id');
        $queryBuilder->orderBy('_rank', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }

    /**
     *  2 - Le réseau qui comptabilisera le plus d’Events programmés sur la prochaine quinzaine de jours (15 jours courants)
     */
    public function getTheMostScheduledEventsOverTheNextDaysNetwork($nextDays, $total, $exclude = [])
    {
        $manager = $this->managerRegistry->getManagerForClass(Network::class);
        $repository = $manager->getRepository(Network::class);

        $queryBuilder = $repository->createQueryBuilder('n');

        $queryBuilder->select('n, COUNT(DISTINCT(e.id)) AS HIDDEN _rank');
        $queryBuilder->leftJoin('n.events', 'e');

        $queryBuilder->andWhere("e.startDate BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), ".$nextDays.", 'day')");
        if ($exclude) {
            $queryBuilder->andWhere('n.id NOT IN (:network)');
            $queryBuilder->setParameter('network', $exclude);
        }

        $queryBuilder->groupBy('n.id');
        $queryBuilder->orderBy('_rank', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }

    /**
     *  3 - Le réseau qui comptabilise le plus d’adhérents au national (indépendamment de l’application)
     */
    public function getTheMostOfflineMembersNetwork($total, array $exclude = [])
    {
        $manager = $this->managerRegistry->getManagerForClass(Network::class);
        $repository = $manager->getRepository(Network::class);

        $queryBuilder = $repository->createQueryBuilder('n');

        $queryBuilder->select('n');
        if ($exclude) {
            $queryBuilder->andWhere('n.id NOT IN (:network)');
            $queryBuilder->setParameter('network', $exclude);
        }

        $queryBuilder->orderBy('n.nbMembersOffline', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }

    /**
     *  4 - Ensuite apparaitront les 7 réseaux les plus actifs des 15 derniers jours (Nombre d’Events + Inscriptions de membres de l’application au réseau)
     */
    public function getTheMostActiveNetworksOfTheLastDays($lastDays, $total, array $exclude = [])
    {
        $manager = $this->managerRegistry->getManagerForClass(Network::class);
        $repository = $manager->getRepository(Network::class);

        $queryBuilder = $repository->createQueryBuilder('n');

        $queryBuilder->select('n, COUNT(DISTINCT(e.id)) + COUNT(DISTINCT(nm.id)) AS HIDDEN _rank');
        $queryBuilder->leftJoin('n.events', 'e');
        $queryBuilder->leftJoin('n.networkMembers', 'nm');

        $queryBuilder->andWhere("e.startDate BETWEEN DATE_SUB(CURRENT_DATE(), ".$lastDays.", 'day') AND CURRENT_DATE()");
        $queryBuilder->andWhere("nm.createdAt BETWEEN DATE_SUB(CURRENT_DATE(), ".$lastDays.", 'day') AND CURRENT_DATE()");
        if ($exclude) {
            $queryBuilder->andWhere('n.id NOT IN (:network)');
            $queryBuilder->setParameter('network', $exclude);
        }

        $queryBuilder->groupBy('n.id');
        $queryBuilder->orderBy('_rank', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }

    /**
     * Nombres totals des réseau
     */
    public function getNumberNetworks()
    {
        $manager = $this->managerRegistry->getManagerForClass(Network::class);
        $repository = $manager->getRepository(Network::class);
        $queryBuilder = $repository->createQueryBuilder('n');
        $queryBuilder->select('COUNT(n)');

        return $queryBuilder;
    }
}
