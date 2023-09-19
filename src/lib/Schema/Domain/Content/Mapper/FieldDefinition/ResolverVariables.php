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

        return str_replace(
            [
                'content',
                'location',
                'item',
                'field',
            ],
            [
                'value.getContent()',
                'value.getLocation()',
                'value',
                'resolver("ItemFieldValue", [value, "' . $fieldDefinition->identifier . '", args])',
            ],
            $resolver
        );
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

class_alias(ResolverVariables::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Mapper\FieldDefinition\ResolverVariables');
