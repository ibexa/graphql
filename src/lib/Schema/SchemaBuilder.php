<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema;

interface SchemaBuilder
{
    public function build(array &$schema);
}

class_alias(SchemaBuilder::class, 'EzSystems\EzPlatformGraphQL\Schema\SchemaBuilder');
