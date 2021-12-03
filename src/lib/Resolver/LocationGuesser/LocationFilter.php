<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

interface LocationFilter
{
    /**
     * Given a Content and a LocationList, filters out locations.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param LocationList $locationList
     */
    public function filter(Content $content, LocationList $locationList): void;
}

class_alias(LocationFilter::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\LocationGuesser\LocationFilter');
