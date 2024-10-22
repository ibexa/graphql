<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use Ibexa\GraphQL\DataLoader\ContentLoader;
use Ibexa\GraphQL\InputMapper\SearchQueryMapper;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;

/**
 * @internal
 */
class SearchResolver
{
    /**
     * @var \Ibexa\GraphQL\InputMapper\SearchQueryMapper
     */
    private $queryMapper;

    /**
     * @var \Ibexa\GraphQL\DataLoader\ContentLoader
     */
    private $contentLoader;

    public function __construct(ContentLoader $contentLoader, SearchQueryMapper $queryMapper)
    {
        $this->contentLoader = $contentLoader;
        $this->queryMapper = $queryMapper;
    }

    public function searchContent($args)
    {
        return $this->contentLoader->find(
            $this->queryMapper->mapInputToQuery($args['query'])
        );
    }

    public function searchContentOfTypeAsConnection($contentTypeIdentifier, $args)
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
