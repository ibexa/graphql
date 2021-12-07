<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

interface LocationProvider
{
    public function getLocations(Content $content): LocationList;
}

class_alias(LocationProvider::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\LocationGuesser\LocationProvider');
