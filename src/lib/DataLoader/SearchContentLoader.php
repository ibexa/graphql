<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\DataLoader;

use Ibexa\Contracts\Core\Repository\Exceptions as ApiException;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\GraphQL\DataLoader\Exception\ArgumentsException;

/**
 * @internal
 */
class SearchContentLoader implements ContentLoader
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
     * Loads a list of content items given a Query Criterion.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query A Query Criterion. To use multiple criteria, group them with a LogicalAnd.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function find(Query $query): array
    {
        return array_map(
            static function (SearchHit $searchHit) {
                return $searchHit->valueObject;
            },
            $this->searchService->findContent($query)->searchHits
        );
    }

    /**
     * Loads a single content item given a Query Criterion.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $filter A Query Criterion. Use Criterion\ContentId, Criterion\RemoteId or Criterion\LocationId for basic loading.
     *
     * @throws \Ibexa\GraphQL\DataLoader\Exception\ArgumentsException
     */
    public function findSingle(Criterion $filter): Content
    {
        try {
            return $this->searchService->findSingle($filter);
        } catch (ApiException\InvalidArgumentException $e) {
        } catch (ApiException\NotFoundException $e) {
            throw new ArgumentsException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Counts the results of a query.
     *
     * @return int
     *
     * @throws \Ibexa\GraphQL\DataLoader\Exception\ArgumentsException
     */
    public function count(Query $query)
    {
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $countQuery->offset = 0;

        try {
            return $this->searchService->findContent($countQuery)->totalCount;
        } catch (ApiException\InvalidArgumentException $e) {
            throw new ArgumentsException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

class_alias(SearchContentLoader::class, 'EzSystems\EzPlatformGraphQL\GraphQL\DataLoader\SearchContentLoader');
