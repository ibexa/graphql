<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\GraphQL\DependencyInjection\GraphQL;

/**
 * Provides schema definitions.
 */
class YamlSchemaProvider implements SchemaProvider
{
    public const DXP_SCHEMA_PATH = 'ibexa/';
    public const DXP_SCHEMA_FILE = self::DXP_SCHEMA_PATH . 'Domain.types.yaml';
    public const DXP_MUTATION_FILE = self::DXP_SCHEMA_PATH . 'ItemMutation.types.yaml';
    public const APP_QUERY_SCHEMA_FILE = 'Query.types.yaml';
    public const APP_MUTATION_SCHEMA_FILE = 'Mutation.types.yaml';

    /**
     * The path to the graphql configuration root.
     */
    private string $root;

    public function __construct($graphQLConfigRoot)
    {
        $this->root = rtrim($graphQLConfigRoot, '/') . '/';
    }

    public function getSchemaConfiguration(): array
    {
        return [
            'query' => $this->getQuerySchema(),
            'mutation' => $this->getMutationSchema(),
            'types' => ['UntypedContent'],
        ];
    }

    private function getQuerySchema(): string
    {
        if (file_exists($this->getAppQuerySchema())) {
            return 'Query';
        } elseif (file_exists($this->getPlatformQuerySchema())) {
            return 'Domain';
        } else {
            return 'Platform';
        }
    }

    private function getMutationSchema(): ?string
    {
        if (file_exists($this->getAppMutationSchemaFile())) {
            return 'Mutation';
        } elseif (file_exists($this->getPlatformMutationSchema())) {
            return 'ItemMutation';
        } else {
            return null;
        }
    }

    private function getAppQuerySchema(): string
    {
        return $this->root . self::APP_QUERY_SCHEMA_FILE;
    }

    private function getAppMutationSchemaFile(): string
    {
        return $this->root . self::APP_MUTATION_SCHEMA_FILE;
    }

    private function getPlatformQuerySchema(): string
    {
        return $this->root . self::DXP_SCHEMA_FILE;
    }

    private function getPlatformMutationSchema(): string
    {
        return $this->root . self::DXP_MUTATION_FILE;
    }
}
