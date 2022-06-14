<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver;

use Ibexa\GraphQL\GraphQL\Mapper\ImageAssetMapperStrategyInterface;
use Ibexa\GraphQL\Value\Field;

/**
 * @internal
 */
class ImageAssetFieldResolver
{
    /* @var array<\Ibexa\Core\FieldType\ImageAsset\ImageAssetMapperStrategyInterface> */
    private $strategies;

    /**
     * @param iterable<\Ibexa\Core\FieldType\ImageAsset\ImageAssetMapperStrategyInterface> $strategies
     */
    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            if ($strategy instanceof ImageAssetMapperStrategyInterface) {
                $this->strategies[] = $strategy;
            }
        }
    }

    public function resolveDomainImageAssetFieldValue(Field $field): ?Field
    {
        $destinationContentId = $field->value->destinationContentId;

        if ($destinationContentId === null) {
            return null;
        }

        foreach ($this->strategies as $strategy) {
            if ($strategy->canProcess($field->value)) {
                $assetField = $strategy->process($field->value);

                return Field::fromField($assetField);
            }
        }

        return null;
    }
}

class_alias(ImageAssetFieldResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\ImageAssetFieldResolver');
