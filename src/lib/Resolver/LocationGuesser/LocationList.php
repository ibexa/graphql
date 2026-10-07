<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\Exception\MultipleValidLocationsException;
use Ibexa\GraphQL\Exception\NoValidLocationsException;

/**
 * List of locations used by the LocationGuesser.
 */
interface LocationList
{
    public function addLocation(Location $location): void;

    /**
     * @return Location
     *
     * @throws MultipleValidLocationsException
     * @throws NoValidLocationsException
     */
    public function getLocation(): Location;

    /**
     * @return Location[]
     */
    public function getLocations(): array;

    public function hasOneLocation(): bool;

    public function removeLocation(Location $location): void;
}

class_alias(LocationList::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\LocationGuesser\LocationList');
