<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

/**
 * Selects a location, if there are several, by picking the main one if it is part of the current tree root.
 */
class MainLocationFilter implements LocationFilter
{
    public function filter(Content $content, LocationList $locationList): void
    {
        foreach ($locationList->getLocations() as $location) {
            if ($location->id !== $content->contentInfo->mainLocationId) {
                $locationList->removeLocation($location);
            }
        }
    }
}

class_alias(MainLocationFilter::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\LocationGuesser\MainLocationFilter');
