<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;

/**
 * List of locations used by the LocationGuesser.
 */
interface LocationList
{
    public function addLocation(Location $location): void;

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     *
     * @throws \Ibexa\GraphQL\Exception\MultipleValidLocationsException
     * @throws \Ibexa\GraphQL\Exception\NoValidLocationsException
     */
    public function getLocation(): Location;

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    public function getLocations(): array;

    public function hasOneLocation(): bool;

    public function removeLocation(Location $location): void;
}
