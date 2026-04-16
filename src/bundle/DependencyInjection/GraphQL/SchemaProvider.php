<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\GraphQL\DependencyInjection\GraphQL;

/**
 * Provides schema definitions.
 */
interface SchemaProvider
{
    /**
     * Returns the overblog graphql schema configuration.
     *
     * @return array Array with the keys from overblog graphql config: query, mutation, types...
     */
    public function getSchemaConfiguration();
}
