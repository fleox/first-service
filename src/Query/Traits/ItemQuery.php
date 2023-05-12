<?php

namespace App\Query\Traits;

use GraphQL\Executor\Promise\Promise;
use Overblog\DataLoader\DataLoader;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\DependencyInjection\Container;
use TypeError;

/**
 * Adds one item query.
 */
trait ItemQuery
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    #[GQL\Query(name: self::GRAPHQL_QUERY_NAME, type: self::GRAPHQL_TYPE)]
    public function getOneItem(int $id): Promise
    {
        // add id to loader queue
        $service = $this->container->get(strtolower(self::GRAPHQL_TYPE . '_loader'));

        if (!$service instanceof DataLoader) {
            throw new TypeError(sprintf('Bad instance service %s', self::GRAPHQL_TYPE . '_loader'));
        }

        /** @var Promise $load */
        $load = $service->load(strval($id));

        return $load;
    }
}
