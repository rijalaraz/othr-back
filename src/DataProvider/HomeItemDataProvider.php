<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Event;
use App\Entity\Network;
use App\Entity\User;
use App\Model\Home;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HomeItemDataProvider extends AbstractDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    use UserDataProviderTrait;
    use NetworkDataProviderTrait;
    use EventDataProviderTrait;

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Home::class === $resourceClass;
    }

//    public function getItem(string $resourceClass, string $operationName = null, array $context = [])
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        if ('highlight' !== $this->request->get('id')) {
            throw new NotFoundHttpException();
        }
        $stats = [];

        /**
         * EVENTS HIGHLIGHTS
         */
        $queryBuilder = $this->getNumberEvents();
        $stats['events'] = $this->getNumberByQueryBuilder($queryBuilder, Event::class, $operationName, $context);


        /**
         * EVENTS HIGHLIGHTS
         */
        $queryBuilder = $this->getTheMostRegistrantsEvents(5);
        $events = $this->getCollectionByQueryBuilder($queryBuilder, Event::class, $operationName, $context);


        /**
         * USERS HIGHLIGHTS
         */

        /**
         * Nombres totals d'users connectés
         */
        $queryBuilder = $this->getNumberUsersOnline();
        $stats['online'] = $this->getNumberByQueryBuilder($queryBuilder, User::class, $operationName, $context);

        $limitUser = 10;
        // 1- Le membre qui aura fait le plus de recommandations ces 15 derniers jours
        $queryBuilder = $this->getTheMostRecommanderMemberOverTheLastDays(15, 1);
        $users = $this->getCollectionByQueryBuilder($queryBuilder, User::class, $operationName, $context);

        // 2- Le membre qui aura rencontré le plus d’autre membres ces 15 derniers jours
        $queryBuilder = $this->getTheMostMetMemberOverTheLastDays(15, 1, $users);
        $user2 = $this->getCollectionByQueryBuilder($queryBuilder, User::class, $operationName, $context);
        $users = array_merge($users, $user2);

        // 3- Le membre qui aura été le plus actif ces 15 derniers jours (Recommandations + Rencontres)
        $queryBuilder = $this->getTheMostActiveMemberInTheLastDays(15, 1, $users);
        $user3 = $this->getCollectionByQueryBuilder($queryBuilder, User::class, $operationName, $context);
        $users = array_merge($users, $user3);

        // 4 - Ensuite apparaitront les 7 nouveaux membres payants de l’application (ordre chronologique)
        $queryBuilder = $this->getTheNewPayingMembers($limitUser - count($users), $users);
        $user4 = $this->getCollectionByQueryBuilder($queryBuilder, User::class, $operationName, $context);
        $users = array_merge($users, $user4);

        /**
         * NETWORKS HIGHLIGHTS
         */
        $queryBuilder = $this->getNumberNetworks();
        $stats['networks'] = $this->getNumberByQueryBuilder($queryBuilder, Network::class, $operationName, $context);

        $limitNetwork = 10;
        // 1- Le réseau qui compte le plus de membres de l’application ET de leur réseau (au national).
        $queryBuilder = $this->getTheMostMembersAndOfflineMembersNetwork(1);
        $networks = $this->getCollectionByQueryBuilder($queryBuilder, Network::class, $operationName, $context);

        // 2 - Le réseau qui comptabilisera le plus d’Events programmés sur la prochaine quinzaine de jours (15 jours courants)
        $queryBuilder = $this->getTheMostScheduledEventsOverTheNextDaysNetwork(15, 1, $networks);
        $network2 = $this->getCollectionByQueryBuilder($queryBuilder, Network::class, $operationName, $context);
        $networks = array_merge($networks, $network2);

        // 3 - Le réseau qui comptabilise le plus d’adhérents au national (indépendamment de l’application)
        $queryBuilder = $this->getTheMostOfflineMembersNetwork(1, $networks);
        $network3 = $this->getCollectionByQueryBuilder($queryBuilder, Network::class, $operationName, $context);
        $networks = array_merge($networks, $network3);

        // 4 - Ensuite apparaitront les 7 réseaux les plus actifs des 15 derniers jours (Nombre d’Events + Inscriptions de membres de l’application au réseau)
        $queryBuilder = $this->getTheMostActiveNetworksOfTheLastDays(15, $limitNetwork - count($networks), $networks);
        $network4 = $this->getCollectionByQueryBuilder($queryBuilder, Network::class, $operationName, $context);
        $networks = array_merge($networks, $network4);

        /**
         * HOME HIGHLIGHTS
         */
        $home = new Home();
        $home->id = 'highlight';
        $home->users = $users;
        $home->events = $events;
        $home->networks = $networks;
        $home->stats = $stats;

        return $home;
    }
}
