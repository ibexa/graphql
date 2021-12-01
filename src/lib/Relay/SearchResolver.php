<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Relay;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Relay\Connection\Output\ConnectionBuilder;

class SearchResolver
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\SearchService
     */
    private $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @param $args
     *
     * @return \Overblog\GraphQLBundle\Relay\Connection\Output\Connection
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function searchContent($args)
    {
        $queryArg = $args['query'];

        $query = new Query();
        $criteria = [];

        if (isset($queryArg['ContentTypeIdentifier'])) {
            $criteria[] = new Query\Criterion\ContentTypeIdentifier($queryArg['ContentTypeIdentifier']);
        }

        if (isset($queryArg['Text'])) {
            foreach ($queryArg['Text'] as $text) {
                $criteria[] = new Query\Criterion\FullText($text);
            }
        }

        if (count($criteria) === 0) {
            return null;
        }
        $query->filter = count($criteria) > 1 ? new Query\Criterion\LogicalAnd($criteria) : $criteria[0];
        $searchResult = $this->searchService->findContentInfo($query);

        $contentItems = array_map(
            function (SearchHit $hit) {
                return $hit->valueObject;
            },
            $searchResult->searchHits
        );

        $connection = ConnectionBuilder::connectionFromArraySlice(
            $contentItems,
            $args,
            [
                'sliceStart' => 0,
                'arrayLength' => $searchResult->totalCount,
            ]
        );
        $connection->sliceSize = count($contentItems);

        return $connection;
    }
}

class_alias(SearchResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Relay\SearchResolver');
