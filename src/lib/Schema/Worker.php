<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema;

interface Worker
{
    /**
     * Does the work on $schema.
     */
    public function work(Builder $schema, array $args);

    /**
     * Tests the arguments and schema, and says if the worker can work on that state.
     * It includes testing if the worker was already executed.
     *
     * @return bool
     */
    public function canWork(Builder $schema, array $args);
}

class_alias(Worker::class, 'EzSystems\EzPlatformGraphQL\Schema\Worker');
