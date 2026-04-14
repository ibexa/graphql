<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

final class ThumbnailResolver implements QueryInterface
{
    /**
     * @return array|null array with the thumbnail info, or null if no thumbnail could be obtained for that image
     */
    public function resolveThumbnail(?Thumbnail $thumbnail): ?array
    {
        if ($thumbnail === null) {
            return null;
        }

        return [
            'uri' => $thumbnail->resource,
            'width' => $thumbnail->width,
            'height' => $thumbnail->height,
            'mimeType' => $thumbnail->mimeType,
            'alternativeText' => '',
        ];
    }
}
