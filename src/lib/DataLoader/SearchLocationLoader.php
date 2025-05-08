<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\DataLoader;

use Ibexa\Contracts\Core\Repository\Exceptions as ApiException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use Ibexa\GraphQL\DataLoader\Exception\ArgumentsException;

/**
 * @internal
 */
class SearchLocationLoader implements LocationLoader
{
    private SearchService $searchService;

    private LocationService $locationService;

    private URLAliasService $urlAliasService;

    private ConfigResolverInterface $configResolver;

    private UrlAliasGenerator $urlAliasGenerator;

    public function __construct(SearchService $searchService, LocationService $locationService, URLAliasService $urlAliasService, ConfigResolverInterface $configResolver, UrlAliasGenerator $urlAliasGenerator)
    {
        $this->searchService = $searchService;
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
        $this->configResolver = $configResolver;
        $this->urlAliasGenerator = $urlAliasGenerator;
    }

    public function find(LocationQuery $query): array
    {
        return array_map(
            static function (SearchHit $searchHit) {
                return $searchHit->valueObject;
            },
            $this->searchService->findLocations($query)->searchHits
        );
    }

    public function findById($id): Location
    {
        try {
            return $this->locationService->loadLocation($id);
        } catch (ApiException\InvalidArgumentException $e) {
        } catch (ApiException\NotFoundException $e) {
            throw new ArgumentsException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function findByRemoteId($id): Location
    {
        try {
            return $this->locationService->loadLocationByRemoteId($id);
        } catch (ApiException\InvalidArgumentException $e) {
        } catch (ApiException\NotFoundException $e) {
            throw new ArgumentsException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function findByUrlAlias(string $urlAlias): Location
    {
        $alias = $this->getUrlAlias($urlAlias);

        return ($alias->type == URLAlias::LOCATION)
            ? $this->locationService->loadLocation($alias->destination)
            : null;
    }

    /**
     * Counts the results of a query.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     *
     * @return int
     *
     * @throws \Ibexa\GraphQL\DataLoader\Exception\ArgumentsException
     */
    public function count(LocationQuery $query)
    {
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $countQuery->offset = 0;

        try {
            return $this->searchService->findLocations($countQuery)->totalCount;
        } catch (ApiException\InvalidArgumentException $e) {
            throw new ArgumentsException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function getUrlAlias(string $pathinfo): URLAlias
    {
        $rootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');
        $pathPrefix = $this->urlAliasGenerator->getPathPrefixByRootLocationId($rootLocationId);

        if (
            $rootLocationId !== null &&
            !$this->urlAliasGenerator->isUriPrefixExcluded($pathinfo) &&
            $pathPrefix !== '/'
        ) {
            $urlAlias = $pathPrefix . $pathinfo;
        } else {
            $urlAlias = $pathinfo;
        }

        return $this->urlAliasService->lookup($urlAlias);
    }
}
