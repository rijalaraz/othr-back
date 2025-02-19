<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Network;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;

class NetworkHomeCollectionDataProvider extends AbstractDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    use NetworkDataProviderTrait;

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Network::class === $resourceClass && 'home_networks' === $operationName;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $limit = 15;
        // 1- Le réseau qui compte le plus de membres de l’application ET de leur réseau (au national).
        $arrayNetworks = [];
        $queryBuilder = $this->getTheMostMembersAndOfflineMembersNetwork(1);
        $networks = $this->getCollectionByQueryBuilder($queryBuilder, $resourceClass, $operationName, $context);
        if($networks) {
            foreach ($networks as $network) {
                $network->setResultType(Network::THE_MOST_MEMBERS);
            }
        }

        // 2 - Le réseau qui comptabilisera le plus d’Events programmés sur la prochaine quinzaine de jours (15 jours courants)
        $queryBuilder = $this->getTheMostScheduledEventsOverTheNextDaysNetwork(15, 1, $networks);
        $network2 = $this->getCollectionByQueryBuilder($queryBuilder, $resourceClass, $operationName, $context);
        if($network2) {
            foreach ($network2 as $network) {
                $network->setResultType(Network::THE_MOST_EVENTS);
            }
            $networks = array_merge($networks, $network2);
        }

        // 3 - Le réseau qui comptabilise le plus d’adhérents au national (indépendamment de l’application)
        $queryBuilder = $this->getTheMostOfflineMembersNetwork(1, $networks);
        $network3 = $this->getCollectionByQueryBuilder($queryBuilder, $resourceClass, $operationName, $context);
        if($network3) {
            foreach ($network3 as $network) {
                $network->setResultType(Network::THE_MOST_OFFLINE_MEMBERS);
            }
            $networks = array_merge($networks, $network3);
        }

        // 4 - Ensuite apparaitront les 7 réseaux les plus actifs des 15 derniers jours (Nombre d’Events + Inscriptions de membres de l’application au réseau)
        $queryBuilder = $this->getTheMostActiveNetworksOfTheLastDays(15, $limit - count($networks), $networks);
        $network4 = $this->getCollectionByQueryBuilder($queryBuilder, $resourceClass, $operationName, $context);
        if($network4) {
            foreach ($network3 as $network) {
                $network->setResultType(Network::THE_MOST_ACTIVE_NETWORKS);
            }
        }

        return array_merge($networks, $network4);
    }

}
