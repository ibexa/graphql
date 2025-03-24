<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Relay;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Overblog\GraphQLBundle\Relay\Connection\ConnectionBuilder;
use Overblog\GraphQLBundle\Relay\Connection\ConnectionInterface;

class SearchResolver
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @param $args
     *
     * @return \Overblog\GraphQLBundle\Relay\Connection\ConnectionInterface<\Ibexa\Contracts\Core\Repository\Values\ValueObject>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function searchContent(array $args): ConnectionInterface
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

        if (count($criteria) !== 0) {
            $query->filter = count($criteria) > 1 ? new Query\Criterion\LogicalAnd($criteria) : $criteria[0];
        }
        $searchResult = $this->searchService->findContentInfo($query);

        $contentItems = array_map(
            static function (SearchHit $hit) {
                return $hit->valueObject;
            },
            $searchResult->searchHits
        );

        $connectionBuilder = new ConnectionBuilder();

        return $connectionBuilder->connectionFromArraySlice(
            $contentItems,
            $args,
            [
                'sliceStart' => 0,
                'arrayLength' => $searchResult->totalCount,
            ]
        );
    }
}
