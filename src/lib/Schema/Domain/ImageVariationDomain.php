<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain;

use Generator;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\GraphQL\Schema;
use Ibexa\GraphQL\Schema\Builder;

/**
 * Adds configured image variations to the ImageVariationIdentifier type.
 */
class ImageVariationDomain implements Iterator, Schema\Worker
{
    public const TYPE = 'ImageVariationIdentifier';
    public const ARG = 'ImageVariation';

    private ConfigResolverInterface $configResolver;

    private NameValidator $nameValidator;

    public function __construct(
        ConfigResolverInterface $configResolver,
        NameValidator $nameValidator
    ) {
        $this->configResolver = $configResolver;
        $this->nameValidator = $nameValidator;
    }

    public function iterate(): Generator
    {
        foreach ($this->configResolver->getParameter('image_variations') as $identifier => $variation) {
            if (!$this->nameValidator->isValidName($identifier)) {
                $this->nameValidator->generateInvalidNameWarning('Image Variation', $identifier);

                continue;
            }

            yield [self::ARG => ['identifier' => $identifier, 'variation' => $variation]];
        }
    }

    public function init(Builder $schema): void
    {
        $schema->addType(new Builder\Input\Type(self::TYPE, 'enum'));
    }

    public function work(Builder $schema, array $args): void
    {
        $schema->addValueToEnum(
            self::TYPE,
            new Builder\Input\EnumValue($args[self::ARG]['identifier'])
        );
    }

    public function canWork(Builder $schema, array $args): bool
    {
        return isset($args[self::ARG]['identifier']);
    }
}
