<?php


namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\FilterEagerLoadingExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Identifier\IdentifierConverterInterface;
use App\Doctrine\ZoneExtension;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractDataProvider
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    protected $request;

    /**
     * @var array|iterable
     */
    protected $collectionExtensions;

    /**
     * @var array|iterable
     */
    protected $itemExtensions;

    public function __construct(
        ManagerRegistry $managerRegistry,
        RequestStack $requestStack,
        iterable $collectionExtensions = [],
        iterable $itemExtensions = [],
        ZoneExtension $zoneExtension
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->request = $requestStack->getCurrentRequest();
        $this->collectionExtensions = $collectionExtensions;
        $this->itemExtensions = $itemExtensions;
        $this->zoneExtension = $zoneExtension;
    }

    public function getNumberByQueryBuilder(
        QueryBuilder $queryBuilder,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ) {
        $queryNameGenerator = new QueryNameGenerator();
        foreach ($this->collectionExtensions as $extension) {
            if ($extension instanceof ExtensionInterface) {
                $extension->applyToCollection(
                    $queryBuilder,
                    $queryNameGenerator,
                    $resourceClass,
                    $operationName,
                    $context
                );
            }
            if ($extension instanceof QueryResultCollectionExtensionInterface
                && $extension->supportsResult($resourceClass, $operationName, $context)
            ) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getCollectionByQueryBuilder(
        QueryBuilder $queryBuilder,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ) {
        $queryNameGenerator = new QueryNameGenerator();
        foreach ($this->collectionExtensions as $extension) {

            if(!$extension instanceof FilterEagerLoadingExtension || 'others_the_most' !== $operationName) {
                $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
            }

            if ($extension instanceof QueryResultCollectionExtensionInterface
                && $extension->supportsResult($resourceClass, $operationName, $context)) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getItemByQueryBuilder(
        QueryBuilder $queryBuilder,
        string $resourceClass,
        $id,
        string $operationName = null,
        array $context = []
    ) {
        /** @var EntityManagerInterface $manager */
        $manager = $this->managerRegistry->getManagerForClass($resourceClass);

        if ((\is_int($id) || \is_string($id)) && !($context[IdentifierConverterInterface::HAS_IDENTIFIER_CONVERTER] ?? false)) {
            $id = $this->normalizeIdentifiers($id, $manager, $resourceClass);
        }
        if (!\is_array($id)) {
            throw new \InvalidArgumentException(sprintf('$id must be array when "%s" key is set to true in the $context',
                IdentifierConverterInterface::HAS_IDENTIFIER_CONVERTER));
        }
        $identifiers = $id;

        $fetchData = $context['fetch_data'] ?? true;
        if (!$fetchData) {
            return $manager->getReference($resourceClass, $identifiers);
        }

        $repository = $manager->getRepository($resourceClass);
        if (!method_exists($repository, 'createQueryBuilder')) {
            throw new RuntimeException('The repository class must have a "createQueryBuilder" method.');
        }

        $queryNameGenerator = new QueryNameGenerator();
        $doctrineClassMetadata = $manager->getClassMetadata($resourceClass);

        $this->addWhereForIdentifiers($identifiers, $queryBuilder, $doctrineClassMetadata);

        foreach ($this->itemExtensions as $extension) {
            $extension->applyToItem($queryBuilder, $queryNameGenerator, $resourceClass, $identifiers, $operationName,
                $context);

            if ($extension instanceof QueryResultItemExtensionInterface
                && $extension->supportsResult($resourceClass, $operationName, $context)) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
