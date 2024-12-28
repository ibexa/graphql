<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;

/**
 * Maps a Field Definition to its GraphQL components.
 */
class ResolverVariables implements FieldDefinitionMapper
{
    /**
     * @var \Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper
     */
    private $innerMapper;

    public function __construct(FieldDefinitionMapper $innerMapper)
    {
        $this->innerMapper = $innerMapper;
    }

    public function mapToFieldDefinitionType(FieldDefinition $fieldDefinition): string
    {
        return $this->innerMapper->mapToFieldDefinitionType($fieldDefinition);
    }

    public function mapToFieldValueType(FieldDefinition $fieldDefinition): string
    {
        return $this->innerMapper->mapToFieldValueType($fieldDefinition);
    }

    public function mapToFieldValueResolver(FieldDefinition $fieldDefinition): string
    {
        $resolver = $this->innerMapper->mapToFieldValueResolver($fieldDefinition);
        $resolver = str_replace(
            [
                'content',
                'location',
                'item',
            ],
            [
                'value.getContent()',
                'value.getLocation()',
                'value',
            ],
            $resolver
        );

        //we make sure no "field" (case insensitive) keyword in the actual field's identifier gets replaced
        //only syntax like: '@=resolver("MatrixFieldValue", [value, "field_matrix"])' needs to be taken into account
        //where [value, "field_matrix"] stands for the actual field's identifier
        if (preg_match('/value, "(.*field.*)"/i', $resolver) !== 1) {
            $resolver = str_replace(
                'field',
                'query("ItemFieldValue", [value, "' . $fieldDefinition->identifier . '", args])',
                $resolver
            );
        }

        return $resolver;
    }

    public function mapToFieldValueInputType(ContentType $contentType, FieldDefinition $fieldDefinition): ?string
    {
        return $this->innerMapper->mapToFieldValueInputType($contentType, $fieldDefinition);
    }

    public function mapToFieldValueArgsBuilder(FieldDefinition $fieldDefinition): ?string
    {
        return $this->innerMapper->mapToFieldValueArgsBuilder($fieldDefinition);
    }
}
