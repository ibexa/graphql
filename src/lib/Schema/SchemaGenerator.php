<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema;

class SchemaGenerator
{
    /**
     * @var SchemaBuilder[]
     */
    private array $schemaBuilders;

    public function __construct(array $schemaBuilders = [])
    {
        $this->schemaBuilders = $schemaBuilders;
    }

    /**
     * @return array
     */
    public function generate(): array
    {
        $schema = [];
        foreach ($this->schemaBuilders as $builder) {
            $builder->build($schema);
        }

        return $schema;
    }
}
