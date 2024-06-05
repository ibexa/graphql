<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;

/**
 * Returns all the locations the current user has access to.
 */
class AllAllowedLocationProvider implements LocationProvider
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\LocationService
     */
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function getLocations(Content $content): LocationList
    {
        $list = new ObjectStorageLocationList($content);

        foreach ($this->locationService->loadLocations($content->contentInfo) as $location) {
            $list->addLocation($location);
        }

        return $list;
    }
}
