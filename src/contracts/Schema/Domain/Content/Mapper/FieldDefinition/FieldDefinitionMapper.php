<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

/**
 * Maps a Field Definition to its GraphQL components.
 */
interface FieldDefinitionMapper
{
    public function mapToFieldDefinitionType(FieldDefinition $fieldDefinition): ?string;

    public function mapToFieldValueType(FieldDefinition $fieldDefinition): ?string;

    public function mapToFieldValueInputType(ContentType $contentType, FieldDefinition $fieldDefinition): ?string;

    public function mapToFieldValueResolver(FieldDefinition $fieldDefinition): ?string;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     *
     * @return string|null the argsBuilder string, or null if there are none.
     */
    public function mapToFieldValueArgsBuilder(FieldDefinition $fieldDefinition): ?string;
}

class_alias(FieldDefinitionMapper::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper');
