<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema;

interface Initializer
{
    public function init(Builder $schema);
}

class_alias(Initializer::class, 'EzSystems\EzPlatformGraphQL\Schema\Initializer');
