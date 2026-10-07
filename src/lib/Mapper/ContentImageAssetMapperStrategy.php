<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Mapper;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\FieldType\ImageAsset;
use Ibexa\GraphQL\DataLoader\ContentLoader;

final class ContentImageAssetMapperStrategy implements ImageAssetMapperStrategyInterface
{
    private ImageAsset\AssetMapper $assetMapper;

    private ContentLoader $contentLoader;

    public function __construct(
        ImageAsset\AssetMapper $assetMapper,
        ContentLoader $contentLoader
    ) {
        $this->assetMapper = $assetMapper;
        $this->contentLoader = $contentLoader;
    }

    public function canProcess(ImageAsset\Value $value): bool
    {
        return $value->source === null;
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     */
    public function process(ImageAsset\Value $value): Field
    {
        $assetField = $this->assetMapper->getAssetField(
            $this->contentLoader->findSingle(new Criterion\ContentId($value->destinationContentId))
        );

        if (empty($assetField->value->alternativeText)) {
            $assetField->value->alternativeText = $value->alternativeText;
        }

        return $assetField;
    }
}
