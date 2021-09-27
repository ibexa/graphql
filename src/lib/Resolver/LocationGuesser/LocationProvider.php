<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use eZ\Publish\API\Repository\Values\Content\Content;

interface LocationProvider
{
    public function getLocations(Content $content): LocationList;
}

class_alias(LocationProvider::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\LocationGuesser\LocationProvider');
