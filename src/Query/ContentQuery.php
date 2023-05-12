<?php

namespace App\Query;

use App\Entity\Content;
use App\GraphQL\PrimitiveType;
use App\Query\Traits\ItemQuery;
use App\Repository\ContentRepository;
use GraphQL\Executor\Promise\Promise;
use Overblog\DataLoader\DataLoader;
use Overblog\GraphQLBundle\Annotation as GQL;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Component\DependencyInjection\Container;

#[GQL\Provider]
class ContentQuery implements QueryInterface, AliasedInterface
{
    use ItemQuery;

    final public const GRAPHQL_TYPE = Content::TYPE;
    final public const GRAPHQL_QUERY_NAME = 'content';

    public function __construct(
        private readonly Container $container,
        private readonly ContentRepository $contentRepository,
    ) {
    }

    #[GQL\Query(name: 'bySlugContent', type: self::GRAPHQL_TYPE)]
    #[GQL\Arg(name: 'slug', type: PrimitiveType::STRING_REQUIRED)]
    public function bySlugContent(string $slug): Promise
    {
        $content = $this->contentRepository->findEnabledBySlug($slug);
        $service = $this->container->get(strtolower(self::GRAPHQL_TYPE . '_loader'));

        if (!$service instanceof DataLoader) {
            throw new \TypeError(sprintf('Bad instance service %s', self::GRAPHQL_TYPE . '_loader'));
        }
        /** @var Promise $load */
        $load = !$content || !$content->getId() ? $service->load('notFound') : $service->load(strval($content->getId()));

        return $load;
    }

    /**
     * @return array<string, string>
     */
    public static function getAliases(): array
    {
        return [
            'bySlugContent' => 'bySlugContent',
        ];
    }
}
