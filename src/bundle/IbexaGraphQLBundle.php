<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\GraphQL;

use Ibexa\Bundle\GraphQL\DependencyInjection\Compiler;
use Ibexa\Bundle\GraphQL\DependencyInjection\IbexaGraphQLExtension;
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

    public function getContainerExtension()
    {
        return new IbexaGraphQLExtension();
    }
}

class_alias(IbexaGraphQLBundle::class, 'EzSystems\EzPlatformGraphQL\EzSystemsEzPlatformGraphQLBundle');
