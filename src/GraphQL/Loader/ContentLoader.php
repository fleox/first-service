<?php

namespace App\GraphQL\Loader;

use App\Entity\Content;
use App\Repository\ContentRepository;
use GraphQL\Executor\Promise\PromiseAdapter;

/**
 * @extends AbstractLoader<Content>
 */
class ContentLoader extends AbstractLoader
{
    public function __construct(
        PromiseAdapter $promiseAdapter,
        ContentRepository $repository
    ) {
        parent::__construct($promiseAdapter, $repository);
    }
}
