<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\GraphQL\DependencyInjection\Compiler;

use Ibexa\GraphQL\Mutation\InputHandler\FieldType\RichText as RichTextInputHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RichTextInputConvertersPass implements CompilerPassInterface
{
    private const RICHTEXT_INPUT_CONVERTER_TAG = 'ibexa.graphql.richtext.input.converter';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(RichTextInputHandler::class)) {
            return;
        }

        $definition = $container->findDefinition(RichTextInputHandler::class);
        $taggedServices = $container->findTaggedServiceIds(self::RICHTEXT_INPUT_CONVERTER_TAG);
        $handlers = [];
        foreach ($taggedServices as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['format'])) {
                    throw new \LogicException(
                        sprintf(
                            'Service "%s" tagged with "%s" service tag needs a "format" attribute set to the converted format',
                            $serviceId,
                            self::RICHTEXT_INPUT_CONVERTER_TAG
                        )
                    );
                }

                $handlers[$tag['format']] = new Reference($serviceId);
            }
        }

        $definition->setArgument('$inputConverters', $handlers);
    }
}
