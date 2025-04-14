<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use Ibexa\GraphQL\Value\Field;

/**
 * @internal
 */
class ImageAssetFieldResolver
{
    /** @var iterable<\Ibexa\GraphQL\Mapper\ImageAssetMapperStrategyInterface> */
    private iterable $strategies;

    /**
     * @param iterable<\Ibexa\GraphQL\Mapper\ImageAssetMapperStrategyInterface> $strategies
     */
    public function __construct(iterable $strategies = [])
    {
        $this->strategies = $strategies;
    }

    public function resolveDomainImageAssetFieldValue(?Field $field): ?Field
    {
        if ($field === null) {
            return null;
        }

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
