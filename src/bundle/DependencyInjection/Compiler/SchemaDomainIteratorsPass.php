<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\GraphQL\DependencyInjection\Compiler;

use Ibexa\GraphQL\Schema\Generator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SchemaDomainIteratorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(Generator::class)) {
            return;
        }

        $generatorDefinition = $container->getDefinition(Generator::class);

        $iterators = [];
        foreach ($container->findTaggedServiceIds('ibexa.graphql.domain_schema.iterator') as $id => $tags) {
            $iterators[] = new Reference($id);
        }

        $generatorDefinition->setArgument('$iterators', $iterators);
        $container->setDefinition(Generator::class, $generatorDefinition);
    }
}

class_alias(SchemaDomainIteratorsPass::class, 'EzSystems\EzPlatformGraphQL\DependencyInjection\Compiler\SchemaDomainIteratorsPass');
