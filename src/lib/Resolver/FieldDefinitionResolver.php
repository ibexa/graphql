<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

/**
 * @internal
 */
class FieldDefinitionResolver
{
    public function resolveFieldDefinitionName(FieldDefinition $fieldDefinition, $args)
    {
        $languageCode = isset($args['language']) ? $args['language'] : null;

        return $fieldDefinition->getName($languageCode);
    }

    public function resolveFieldDefinitionDescription(FieldDefinition $fieldDefinition, $args)
    {
        $languageCode = isset($args['language']) ? $args['language'] : null;

        return $fieldDefinition->getDescription($languageCode);
    }

    /**
     * @return array{index: (int|string), label: mixed}[]
     */
    public function resolveSelectionFieldDefinitionOptions(array $options): array
    {
        $return = [];

        foreach ($options as $index => $label) {
            $return[] = ['index' => $index, 'label' => $label];
        }

        return $return;
    }
}
