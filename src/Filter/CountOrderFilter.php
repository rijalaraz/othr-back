<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class CountOrderFilter extends OrderFilter
{
    protected function filterProperty(string $property, $direction, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null)
    {
        $direction = $this->normalizeValue($direction, $property);
        if (null === $direction) {
            return;
        }
        
        $alias = $queryBuilder->getRootAliases()[0];
        $field = $property;

        $nullRankHiddenField = sprintf('_%s_%s_null_rank', $alias, $field);
        
        $queryBuilder->select(sprintf('%s, COUNT(DISTINCT(v.id)) AS HIDDEN %s', $alias, $nullRankHiddenField));
        $queryBuilder->leftJoin(sprintf('%s.%s',$alias, $field), 'v');
        $queryBuilder->groupBy(sprintf('%s.id', $alias));
        $queryBuilder->orderBy($nullRankHiddenField, $direction);
    }

    private function normalizeValue($value, string $property): ?string
    {
        if (empty($value) && null !== $defaultDirection = $this->getProperties()[$property]['default_direction'] ?? null) {
            // fallback to default direction
            $value = $defaultDirection;
        }

        $value = strtoupper($value);
        if (!\in_array($value, [self::DIRECTION_ASC, self::DIRECTION_DESC], true)) {
            return null;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = $this->getProperties();

        foreach ($properties as $property => $propertyOptions) {
            $propertyName = $this->normalizePropertyName($property);
            $description[sprintf('%s[%s]', $this->orderParameterName, $propertyName)] = [
                'property' => $propertyName,
                'type' => 'string',
                'required' => false,
                'schema' => [
                    'type' => 'string',
                    'enum' => [
                        strtolower(OrderFilterInterface::DIRECTION_ASC),
                        strtolower(OrderFilterInterface::DIRECTION_DESC),
                    ],
                ],
            ];
        }

        return $description;
    }

}