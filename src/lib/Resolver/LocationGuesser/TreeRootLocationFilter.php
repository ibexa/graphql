<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\Repository\TreeRootLocationResolver;

/**
 * Filters a Location based on the tree root site settings.
 * Only locations that are within the site root or one of the excluded paths are kept.
 */
class TreeRootLocationFilter implements LocationFilter
{
    private TreeRootLocationResolver $treeRootLocationResolver;

    public function __construct(TreeRootLocationResolver $treeRootLocationResolver)
    {
        $this->treeRootLocationResolver = $treeRootLocationResolver;
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
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function locationIsInTreeRoot(Location $location): bool
    {
        $rootLocation = $this->treeRootLocationResolver->resolveRootLocation();

        return $this->containsRootPath($location->path, $rootLocation->path);
    }

    /**
     * Tests if the location is excluded from tree root.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function locationPrefixIsExcluded(Location $candidateLocation): bool
    {
        foreach ($this->treeRootLocationResolver->resolveExcludedLocations() as $excludedLocation) {
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
