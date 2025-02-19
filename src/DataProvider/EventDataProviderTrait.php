<?php

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use App\Entity\Event;
use App\Entity\Payment;
use App\Entity\UserEvent;

trait EventDataProviderTrait
{
    /**
     * Ici apparaitront les Events mis en avant. La mise en avant sera dans un premier temps défini par
     * le plus grand nombre de participants. Les 3 ou 5 Events qui ont le plus d'inscrits seront mis en avant.
     */
    public function getTheMostRegistrantsEvents($total)
    {
        $manager = $this->managerRegistry->getManagerForClass(Event::class);
        $repository = $manager->getRepository(Event::class);

        $queryBuilder = $repository->createQueryBuilder('e');

        $queryBuilder->select('e, SUM(uv.nbPlaces) AS HIDDEN _rank');
        $queryBuilder->leftJoin('e.userEvents', 'uv');
        $queryBuilder->leftJoin('uv.payment', 'p');

        $queryBuilder->andWhere('p.paymentStatus != :status');
        $queryBuilder->setParameter('status', Payment::STATUS_CANCELED);

        $queryBuilder->groupBy('e.id');
        $queryBuilder->orderBy('_rank', 'DESC');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults($total);

        return $queryBuilder;
    }

    public function getNumberEvents()
    {
        $manager = $this->managerRegistry->getManagerForClass(Event::class);
        $repository = $manager->getRepository(Event::class);
        $queryBuilder = $repository->createQueryBuilder('e');
        $queryBuilder->select('COUNT(e)');

        return $queryBuilder;
    }
}
