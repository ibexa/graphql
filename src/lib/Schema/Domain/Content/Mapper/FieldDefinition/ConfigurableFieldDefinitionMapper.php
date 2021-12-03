<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;

class ConfigurableFieldDefinitionMapper implements FieldDefinitionMapper, FieldDefinitionInputMapper, FieldDefinitionArgsBuilderMapper
{
    /**
     * @var array
     * Map of ez field type identifier to a GraphQL type
     */
    private $typesMap;

    /**
     * @var \Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper
     */
    private $innerMapper;

    public function __construct(FieldDefinitionMapper $innerMapper, $typesMap = [])
    {
        $this->typesMap = $typesMap;
        $this->innerMapper = $innerMapper;
    }

    public function mapToFieldValueType(FieldDefinition $fieldDefinition): ?string
    {
        return $this->typesMap[$fieldDefinition->fieldTypeIdentifier]['value_type']
            ?? $this->innerMapper->mapToFieldValueType($fieldDefinition);
    }

    public function mapToFieldValueInputType(ContentType $contentType, FieldDefinition $fieldDefinition): ?string
    {
        if (isset($this->typesMap[$fieldDefinition->fieldTypeIdentifier]['input_type'])) {
            return $this->typesMap[$fieldDefinition->fieldTypeIdentifier]['input_type'];
        }

        return $this->innerMapper->mapToFieldValueInputType($contentType, $fieldDefinition);
    }

    public function mapToFieldDefinitionType(FieldDefinition $fieldDefinition): ?string
    {
        return $this->typesMap[$fieldDefinition->fieldTypeIdentifier]['definition_type']
            ?? $this->innerMapper->mapToFieldDefinitionType($fieldDefinition);
    }

    public function mapToFieldValueResolver(FieldDefinition $fieldDefinition): ?string
    {
        return isset($this->typesMap[$fieldDefinition->fieldTypeIdentifier]['value_resolver']) ?
            '@=' . $this->typesMap[$fieldDefinition->fieldTypeIdentifier]['value_resolver']
            : $this->innerMapper->mapToFieldValueResolver($fieldDefinition);
    }

    public function mapToFieldValueArgsBuilder(FieldDefinition $fieldDefinition): ?string
    {
        return $this->typesMap[$fieldDefinition->fieldTypeIdentifier]['argsBuilder'] ?? null;
    }
}

class_alias(ConfigurableFieldDefinitionMapper::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Mapper\FieldDefinition\ConfigurableFieldDefinitionMapper');
