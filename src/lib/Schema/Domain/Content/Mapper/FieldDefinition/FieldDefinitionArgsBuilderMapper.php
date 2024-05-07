<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

/**
 * Maps a Field Definition to its GraphQL arguments.
 *
 * @deprecated since 2.0, will be removed in 3.0. Use the FieldDefinitionMapper interface instead.
 */
interface FieldDefinitionArgsBuilderMapper
{
}

class_alias(FieldDefinitionArgsBuilderMapper::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionArgsBuilderMapper');
