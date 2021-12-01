<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\FieldType\ImageAsset\AssetMapper;
use Ibexa\GraphQL\DataLoader\ContentLoader;
use Ibexa\GraphQL\Value\Field;

/**
 * @internal
 */
class ImageAssetFieldResolver
{
    /**
     * @var DomainContentResolver
     */
    private $domainContentResolver;
    /**
     * @var \Ibexa\GraphQL\DataLoader\ContentLoader
     */
    private $contentLoader;
    /**
     * @var \Ibexa\Core\FieldType\ImageAsset\AssetMapper
     */
    private $assetMapper;

    public function __construct(ContentLoader $contentLoader, DomainContentResolver $domainContentResolver, AssetMapper $assetMapper)
    {
        $this->domainContentResolver = $domainContentResolver;
        $this->contentLoader = $contentLoader;
        $this->assetMapper = $assetMapper;
    }

    public function resolveDomainImageAssetFieldValue(Field $field)
    {
        $destinationContentId = $field->value->destinationContentId;

        if ($destinationContentId === null) {
            return null;
        }

        $assetField = $this->assetMapper->getAssetField(
            $this->contentLoader->findSingle(new Criterion\ContentId($destinationContentId))
        );

        if (empty($assetField->value->alternativeText)) {
            $assetField->value->alternativeText = $field->value->alternativeText;
        }

        return Field::fromField($assetField);
    }
}

class_alias(ImageAssetFieldResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\ImageAssetFieldResolver');
