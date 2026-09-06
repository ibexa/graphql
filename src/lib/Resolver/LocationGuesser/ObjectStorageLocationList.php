<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\Exception;
use SplObjectStorage;

final class ObjectStorageLocationList implements LocationList
{
    /**
     * The content item locations were guessed for.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    private $content;

    /**
     * @var \SplObjectStorage
     */
    private $locations;

    public function __construct(Content $content)
    {
        $this->content = $content;
        $this->locations = new SplObjectStorage();
    }

    public function addLocation(Location $location): void
    {
        $this->locations->attach($location);
    }

    public function getLocation(): Location
    {
        if (count($this->locations) === 1) {
            return current($this->locations);
        } elseif (count($this->locations) > 1) {
            throw new Exception\MultipleValidLocationsException($this->content, \iterator_to_array($this->locations));
        } elseif (count($this->locations) === 0) {
            throw new Exception\NoValidLocationsException($this->content);
        }
    }

    public function getLocations(): array
    {
        return \iterator_to_array($this->locations);
    }

    public function hasOneLocation(): bool
    {
        return count($this->locations) === 1;
    }

    public function removeLocation(Location $location): void
    {
        $this->locations->detach($location);
    }
}

class_alias(ObjectStorageLocationList::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\LocationGuesser\ObjectStorageLocationList');
