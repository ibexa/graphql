<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\GraphQL\DataLoader\ContentLoader;
use Ibexa\GraphQL\InputMapper\SearchQueryMapper;
use Overblog\GraphQLBundle\Definition\ArgumentInterface;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;

/**
 * @internal
 */
class SearchResolver
{
    private SearchQueryMapper $queryMapper;

    private ContentLoader $contentLoader;

    public function __construct(ContentLoader $contentLoader, SearchQueryMapper $queryMapper)
    {
        $this->contentLoader = $contentLoader;
        $this->queryMapper = $queryMapper;
    }

    /**
     * @param array{
     *     query: array{
     *         offset?: int,
     *         limit?: int,
     *         ContentTypeIdentifier?: string|string[],
     *         Text?: string,
     *         Field?: array<array{target: mixed}>,
     *         ParentLocationId?: int|int[],
     *         sortBy?: array<string|\Ibexa\Contracts\Core\Repository\Values\Content\Query::SORT_*>,
     *         Modified?: array<string, mixed>,
     *         Created?: array<string, mixed>
     *     }
     * } $args
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content[]
     */
    public function searchContent(array $args): array
    {
        return $this->contentLoader->find(
            $this->queryMapper->mapInputToQuery($args['query'])
        );
    }

    /**
     * @param string $contentTypeIdentifier
     * @return \GraphQL\Executor\Promise\Promise|\Overblog\GraphQLBundle\Relay\Connection\Output\Connection<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     */
    public function searchContentOfTypeAsConnection($contentTypeIdentifier, ArgumentInterface $args)
    {
        $query = $args['query'] ?: [];
        $query['ContentTypeIdentifier'] = $contentTypeIdentifier;
        $query['sortBy'] = $args['sortBy'];
        $query = $this->queryMapper->mapInputToQuery($query);

        $paginator = new Paginator(function ($offset, $limit) use ($query) {
            $query->offset = $offset;
            $query->limit = $limit ?? 10;

            return $this->contentLoader->find($query);
        });

        return $paginator->auto(
            $args,
            function () use ($query) {
                return $this->contentLoader->count($query);
            }
        );
    }
}
