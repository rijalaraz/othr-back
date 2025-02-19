<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Event;
use App\Entity\Network;
use App\Entity\Relationship;
use App\Entity\User;
use App\Entity\UserEvent;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class ZoneExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null
    ) {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?string $operationName = null,
        array $context = []
    ) {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ((Relationship::class !== $resourceClass && Network::class !== $resourceClass && Event::class !== $resourceClass && User::class !== $resourceClass) || $this->security->isGranted('ROLE_SUPER_ADMIN') || null === $user = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->setParameter('zones', $user->getZones());
        if (User::class === $resourceClass) {
            $queryBuilder->innerJoin(sprintf('%s.%s', $rootAlias, 'zones'), 'z')
                ->andWhere(sprintf('%s.%s IN (:zones)', 'z', 'id'));
        } elseif (Relationship::class === $resourceClass) {
            $queryBuilder->innerJoin(sprintf('%s.%s', $rootAlias, 'sourceUser'), 'su');
            $queryBuilder->innerJoin(sprintf('%s.%s', 'su', 'zones'), 'sz');
            $queryBuilder->innerJoin(sprintf('%s.%s', $rootAlias, 'targetUser'), 'tu');
            $queryBuilder->innerJoin(sprintf('%s.%s', 'tu', 'zones'), 'tz')
                ->andWhere(sprintf('%s.%s IN (:zones)', 'sz', 'id'))
                ->andWhere(sprintf('%s.%s IN (:zones)', 'tz', 'id'));
        } else {
            $queryBuilder->andWhere(sprintf('%s.%s IN (:zones)', $rootAlias, 'zone'));
        }
    }

}
