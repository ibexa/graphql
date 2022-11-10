<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;

final class ContentThumbnailResolver
{
    /** @var \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy */
    private $thumbnailStrategy;

    public function __construct(
        ThumbnailStrategy $thumbnailStrategy
    ) {
        $this->thumbnailStrategy = $thumbnailStrategy;
    }

    /**
     * @return array|null array with the thumbnail info, or null if no thumbnail could be obtained for that image
     */
    public function resolveContentThumbnail(Content $content): ?array
    {
        $thumbnail = $this->thumbnailStrategy->getThumbnail(
            $content->getContentType(),
            $content->getFields(),
            $content->getVersionInfo()
        );

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

class_alias(ContentThumbnailResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\ContentThumbnailResolver');
