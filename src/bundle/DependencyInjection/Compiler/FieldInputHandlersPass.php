<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\GraphQL\DependencyInjection\Compiler;

use Ibexa\GraphQL\Resolver\DomainContentMutationResolver;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\FieldDefinition\AddFieldDefinitionToItemMutation;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FieldInputHandlersPass implements CompilerPassInterface
{
    private const FIELD_TYPE_INPUT_HANDLER_TAG = 'ibexa.graphql.field_type.input.handler';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(DomainContentMutationResolver::class)) {
            return;
        }

        if (!$container->has(AddFieldDefinitionToItemMutation::class)) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds(self::FIELD_TYPE_INPUT_HANDLER_TAG);
        $handlers = [];
        foreach ($taggedServices as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['fieldtype'])) {
                    throw new \LogicException(
                        sprintf(
                            'Service "%s" tagged with "%s" service tag needs a "fieldtype" attribute set to the Field Type\'s identifier',
                            $serviceId,
                            self::FIELD_TYPE_INPUT_HANDLER_TAG
                        )
                    );
                }

                $handlers[$tag['fieldtype']] = new Reference($serviceId);
            }
        }

        $container->findDefinition(DomainContentMutationResolver::class)->setArgument('$fieldInputHandlers', $handlers);
    }
}
