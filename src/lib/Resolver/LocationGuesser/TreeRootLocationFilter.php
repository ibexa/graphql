<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

/**
 * Filters a Location based on the tree root site settings.
 * Only locations that are within the site root or one of the excluded paths are kept.
 */
class TreeRootLocationFilter implements LocationFilter
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var \Ibexa\Contracts\Core\Repository\URLAliasService
     */
    private $urlAliasService;

    public function __construct(LocationService $locationService, URLAliasService $urlAliasService, ConfigResolverInterface $configResolver)
    {
        $this->locationService = $locationService;
        $this->configResolver = $configResolver;
        $this->urlAliasService = $urlAliasService;
    }

    public function filter(Content $content, LocationList $locationList): void
    {
        foreach ($locationList->getLocations() as $location) {
            if (!$this->locationIsInTreeRoot($location) && !$this->locationPrefixIsExcluded($location)) {
                $locationList->removeLocation($location);
            }
        }
    }

    /**
     * Checks if a location is valid in regards to the tree root setting.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function locationIsInTreeRoot(Location $location): bool
    {
        static $rootLocations = [];

        $treeRootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');
        if (!isset($rootLocations[$treeRootLocationId])) {
            $rootLocations[$treeRootLocationId] = $this->locationService->loadLocation($treeRootLocationId);
        }

        return $this->containsRootPath($location->path, $rootLocations[$treeRootLocationId]->path);
    }

    /**
     * Tests if the location is excluded from tree root.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $candidateLocation
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function locationPrefixIsExcluded(Location $candidateLocation): bool
    {
        static $excludedLocations = null;

        if ($excludedLocations === null) {
            $excludedUriPrefixes = $this->configResolver->getParameter('content.tree_root.excluded_uri_prefixes');
            if (empty($excludedUriPrefixes)) {
                return false;
            }
            foreach ($excludedUriPrefixes as $uri) {
                $urlAlias = $this->urlAliasService->lookup($uri);
                if ($urlAlias->type === URLAlias::LOCATION) {
                    $excludedLocations[] = $this->locationService->loadLocation($urlAlias->destination);
                }
            }
        }

        foreach ($excludedLocations as $excludedLocation) {
            if ($this->containsRootPath($candidateLocation->path, $excludedLocation->path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $path
     * @param array $rootPath
     */
    private function containsRootPath(array $path, array $rootPath): bool
    {
        return array_slice($path, 0, count($rootPath)) === $rootPath;
    }
}

class_alias(TreeRootLocationFilter::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\LocationGuesser\TreeRootLocationFilter');
