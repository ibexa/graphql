<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\GraphQL;

use Ibexa\Bundle\GraphQL\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaGraphQLBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Compiler\FieldInputHandlersPass());
        $container->addCompilerPass(new Compiler\RichTextInputConvertersPass());
        $container->addCompilerPass(new Compiler\SchemaWorkersPass());
        $container->addCompilerPass(new Compiler\SchemaDomainIteratorsPass());
    }
}

class_alias(IbexaGraphQLBundle::class, 'EzSystems\EzPlatformGraphQL\EzSystemsEzPlatformGraphQLBundle');