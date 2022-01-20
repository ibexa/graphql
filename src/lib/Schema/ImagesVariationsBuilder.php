<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigResolver;

/**
 * Generates the ImageVariationIdentifier enum that indexes images variations identifiers.
 *
 * @deprecated since 1.0, will be removed in 4.0.
 */
class ImagesVariationsBuilder implements SchemaBuilder
{
    /**
     * @var \Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigResolver
     */
    private $configResolver;

    public function __construct(ConfigResolver $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function build(array &$schema)
    {
        $schema['ImageVariationIdentifier'] = [
            'type' => 'enum',
            'config' => [
                'values' => [],
            ],
        ];

        $values = &$schema['ImageVariationIdentifier']['config']['values'];

        foreach (array_keys($this->configResolver->getParameter('image_variations')) as $variationIdentifier) {
            $values[$variationIdentifier] = [];
        }
    }
}

class_alias(ImagesVariationsBuilder::class, 'EzSystems\EzPlatformGraphQL\Schema\ImagesVariationsBuilder');
