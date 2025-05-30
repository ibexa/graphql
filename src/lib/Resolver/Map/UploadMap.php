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
    /**
     * @return array<string, array<string, callable(): \Overblog\GraphQLBundle\Upload\Type\GraphQLUploadType>>
     */
    protected function map(): array
    {
        return [
            'FileUpload' => [
                self::SCALAR_TYPE => static fn (): GraphQLUploadType => new GraphQLUploadType(),
            ],
        ];
    }
}
