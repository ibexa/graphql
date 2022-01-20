<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

/**
 * Maps a Field Definition to its GraphQL components for input (mutations).
 *
 * @deprecated since 2.0, will be removed in 3.0. Use the FieldDefinitionMapper interface instead.
 */
interface FieldDefinitionInputMapper
{
}

class_alias(FieldDefinitionInputMapper::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionInputMapper');
