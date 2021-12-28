<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Domain;

use Generator;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\GraphQL\Schema;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Domain;

/**
 * Adds configured image variations to the ImageVariationIdentifier type.
 */
class ImageVariationDomain implements Domain\Iterator, Schema\Worker
{
    public const TYPE = 'ImageVariationIdentifier';
    public const ARG = 'ImageVariation';

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function iterate(): Generator
    {
        foreach ($this->configResolver->getParameter('image_variations') as $identifier => $variation) {
            yield [self::ARG => ['identifier' => $identifier, 'variation' => $variation]];
        }
    }

    public function init(Builder $schema)
    {
        $schema->addType(new Builder\Input\Type(self::TYPE, 'enum'));
    }

    public function work(Builder $schema, array $args)
    {
        $schema->addValueToEnum(
            self::TYPE,
            new Builder\Input\EnumValue($args[self::ARG]['identifier'])
        );
    }

    public function canWork(Builder $schema, array $args)
    {
        return isset($args[self::ARG]['identifier']);
    }
}

class_alias(ImageVariationDomain::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\ImageVariationDomain');
