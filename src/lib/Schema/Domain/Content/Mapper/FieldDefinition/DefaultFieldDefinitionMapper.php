<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;

class DefaultFieldDefinitionMapper implements FieldDefinitionMapper
{
    public function mapToFieldValueType(FieldDefinition $fieldDefinition): ?string
    {
        return 'String';
    }

    public function mapToFieldValueInputType(ContentType $contentType, FieldDefinition $fieldDefinition): ?string
    {
        return 'String';
    }

    public function mapToFieldDefinitionType(FieldDefinition $fieldDefinition): ?string
    {
        return 'FieldDefinition';
    }

    public function mapToFieldValueResolver(FieldDefinition $fieldDefinition): ?string
    {
        return '@=field';
    }

    public function mapToFieldValueArgsBuilder(FieldDefinition $fieldDefinition): ?string
    {
        return null;
    }
}

class_alias(DefaultFieldDefinitionMapper::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Mapper\FieldDefinition\DefaultFieldDefinitionMapper');
