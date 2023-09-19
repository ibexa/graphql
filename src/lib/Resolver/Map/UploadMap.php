<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver\Map;

use Overblog\GraphQLBundle\Resolver\ResolverMap;
use Overblog\GraphQLBundle\Upload\Type\GraphQLUploadType;

class UploadMap extends ResolverMap
{
    protected function map()
    {
        return [
            'FileUpload' => [self::SCALAR_TYPE => static function () { return new GraphQLUploadType(); }],
        ];
    }
}

class_alias(UploadMap::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\Map\UploadMap');
