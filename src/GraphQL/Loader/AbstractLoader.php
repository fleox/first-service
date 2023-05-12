<?php

namespace App\GraphQL\Loader;

use Doctrine\ORM\EntityRepository;
use GraphQL\Executor\Promise\Promise;
use GraphQL\Executor\Promise\PromiseAdapter;

/** @template Entity of object */
abstract class AbstractLoader
{
    /**
     * @param EntityRepository<Entity> $repository
     */
    public function __construct(
        private readonly PromiseAdapter $promiseAdapter,
        private readonly EntityRepository $repository
    ) {
    }

    /**
     * query every Foos from given ids
     * return Promise containing results.
     *
     * @param int[] $ids
     */
    public function all(array $ids): Promise
    {
        $qb = $this->repository->createQueryBuilder('o');
        $qb->where($qb->expr()->in('o.id', ':ids'));
        $qb->setParameter('ids', array_unique($ids));

        /** @var object[] $entities */
        $entities = $qb->getQuery()->getResult();

        $entitiesMap = array_reduce($entities, function (array $map, object $item) {
            $map[$item->{'get' . ucfirst('id')}()] = $item;

            return $map;
        }, []);

        $results = array_map(fn ($id) => $entitiesMap[$id] ?? null, $ids);

        return $this->promiseAdapter->all($results);
    }
}
