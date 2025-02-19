<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;

class UserTheMostCollectionDataProvider extends AbstractDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    use UserDataProviderTrait;

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass && 'others_the_most' === $operationName;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        switch ($this->request->get('the_most')) {
            case 'provider': // PROVIDER
                $queryBuilder = $this->getTheMostRecommanderMemberOverTheLastDays(15, 10);
                break;
            case 'networker': // NETWORKER
                $queryBuilder = $this->getTheMostEventOthersInTheLastDays(15, 10);
                break;
            case 'energizer': // ENERGIZER
                $queryBuilder = $this->getTheMostActiveOthersInTheLastDays(15, 10);
                break;
            case 'socializer': // SOCIALIZER
            default:
                $queryBuilder = $this->getTheMostMetMemberOverTheLastDays(15, 10);
                break;
        }

        return $this->getCollectionByQueryBuilder($queryBuilder, $resourceClass, $operationName, $context);
    }
}
