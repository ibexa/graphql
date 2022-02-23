<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\GraphQL\DataLoader\Exception\ArgumentsException;
use Ibexa\GraphQL\DataLoader\LocationLoader;
use Ibexa\GraphQL\InputMapper\SearchQuerySortByMapper;
use Ibexa\GraphQL\Relay\PageAwareConnection;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;

/**
 * @internal
 */
class LocationResolver
{
    public const DEFAULT_LIMIT = 10;

    /**
     * @var \Ibexa\Contracts\Core\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \Ibexa\GraphQL\DataLoader\LocationLoader
     */
    private $locationLoader;

    /**
     * @var \Ibexa\GraphQL\InputMapper\SearchQuerySortByMapper
     */
    private $sortMapper;

    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        LocationLoader $locationLoader,
        SearchQuerySortByMapper $sortMapper
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->locationLoader = $locationLoader;
        $this->sortMapper = $sortMapper;
    }

    public function resolveLocation($args)
    {
        if (isset($args['locationId'])) {
            return $this->locationLoader->findById($args['locationId']);
        } elseif (isset($args['remoteId'])) {
            return $this->locationLoader->findByRemoteId($args['remoteId']);
        } elseif (isset($args['urlAlias'])) {
            return $this->locationLoader->findByUrlAlias($args['urlAlias']);
        }

        throw new ArgumentsException('Requires one and only one of remoteId or locationId');
    }

    public function resolveLocationsByContentId($contentId)
    {
        return $this->locationService->loadLocations(
            $this->contentService->loadContentInfo($contentId)
        );
    }

    public function resolveLocationById($locationId)
    {
        return $this->locationService->loadLocation($locationId);
    }

    /**
     * @param int $locationId
     *
     * @return \Overblog\GraphQLBundle\Relay\Connection\Output\Connection
     */
    public function resolveLocationChildren($locationId, Argument $args): PageAwareConnection
    {
        $args['locationId'] = $locationId;
        $sortClauses = isset($args['sortBy']) ? $this->sortMapper->mapInputToSortClauses($args['sortBy']) : [];

        $query = new LocationQuery([
            'filter' => $this->buildFilter($args),
            'sortClauses' => $sortClauses,
        ]);

        $paginator = new Paginator(function ($offset, $limit) use ($query) {
            $query->offset = $offset;
            $query->limit = $limit ?? self::DEFAULT_LIMIT;

            return $this->locationLoader->find($query);
        });

        return PageAwareConnection::fromConnection(
            $paginator->auto(
                $args,
                function () use ($query) {
                    return $this->locationLoader->count($query);
                }
            ),
            $args
        );
    }

    private function buildFilter(Argument $args): Criterion
    {
        return new Query\Criterion\ParentLocationId($args['locationId']);
    }
}

class_alias(LocationResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\LocationResolver');
