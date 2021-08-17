<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Domain;

use Ibexa\GraphQL\Schema\Builder;
use Generator;

/**
 * Iterates on a domain, and returns sets of items a schema can be generated from.
 */
interface Iterator
{
    /**
     * Performs one-time initializations on the schema.
     *
     * @return
     */
    public function init(Builder $schema);

    /**
     * Returns set of items from the domain.
     *
     * @return \Generator a generator yielding the items
     */
    public function iterate(): Generator;
}

class_alias(Iterator::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Iterator');
