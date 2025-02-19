<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Doctrine\ZoneExtension;
use App\Entity\Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;

class EventHighlightCollectionDataProvider extends AbstractDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    use EventDataProviderTrait;

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Event::class === $resourceClass && 'events_highlight' === $operationName;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $queryBuilder = $this->getTheMostRegistrantsEvents(5);

        return $this->getCollectionByQueryBuilder($queryBuilder, $resourceClass, $operationName, $context);
    }
}
