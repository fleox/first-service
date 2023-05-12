<?php

namespace App\Query\Traits;

use App\GraphQL\PrimitiveType;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Executor\Promise\Promise;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\DependencyInjection\Container;

/**
 * Adds many query.
 */
trait ManyQuery
{
    public function __construct(
        private readonly Container $container,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[GQL\Query(type: '[' . self::GRAPHQL_TYPE . '!]!', name: self::GRAPHQL_QUERY_NAME . 's')]
    #[GQL\Arg(name: 'ids', type: PrimitiveType::ARRAY_INT_REQUIRED)]
    public function getManyItems($ids): Promise
    {
        if (!$ids) {
            $sql = 'SELECT id FROM ' . strtolower(self::GRAPHQL_TYPE);
            $stmt = $this->em->getConnection()->prepare($sql);
            $results = $stmt->executeQuery()->fetchAllAssociative();
            $ids = array_map(
                fn ($item) => $item['id'], $results
            );
        }
        // add every ids to loader queue
        return $this->container->get(strtolower(self::GRAPHQL_TYPE . '_loader'))->loadMany($ids);
    }
}
