<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;

abstract class DecoratingFieldDefinitionMapper implements FieldDefinitionMapper
{
    /**
     * @var \Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper
     */
    protected $innerMapper;

    public function __construct(FieldDefinitionMapper $innerMapper)
    {
        $this->innerMapper = $innerMapper;
    }

    public function mapToFieldDefinitionType(FieldDefinition $fieldDefinition): ?string
    {
        return $this->innerMapper->mapToFieldDefinitionType($fieldDefinition);
    }

    public function mapToFieldValueType(FieldDefinition $fieldDefinition): ?string
    {
        return $this->innerMapper->mapToFieldValueType($fieldDefinition);
    }

    public function mapToFieldValueResolver(FieldDefinition $fieldDefinition): ?string
    {
        return $this->innerMapper->mapToFieldValueResolver($fieldDefinition);
    }

    public function mapToFieldValueInputType(ContentType $contentType, FieldDefinition $fieldDefinition): ?string
    {
        return $this->innerMapper->mapToFieldValueInputType($contentType, $fieldDefinition);
    }

    public function mapToFieldValueArgsBuilder(FieldDefinition $fieldDefinition): ?string
    {
        return $this->innerMapper->mapToFieldValueArgsBuilder($fieldDefinition);
    }

    abstract protected function getFieldTypeIdentifier(): string;

    protected function canMap(FieldDefinition $fieldDefinition)
    {
        return $fieldDefinition->fieldTypeIdentifier === $this->getFieldTypeIdentifier();
    }
}
