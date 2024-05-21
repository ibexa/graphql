<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Worker\Language;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Initializer;
use Ibexa\GraphQL\Schema\Worker;

class AddLanguageToEnum implements Worker, Initializer
{
    public const ENUM_NAME = 'RepositoryLanguage';

    public function init(Builder $schema)
    {
        $schema->addType(
            new Builder\Input\Type(
                self::ENUM_NAME,
                'enum'
            )
        );
    }

    /**
     * Does the work on $schema.
     */
    public function work(Builder $schema, array $args)
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $args['Language'];

        $schema->addValueToEnum(
            self::ENUM_NAME,
            new Builder\Input\EnumValue(
                $language->languageCode,
                [
                    'description' => $language->name,
                    'value' => $language->languageCode,
                ]
            )
        );
    }

    /**
     * Tests the arguments and schema, and says if the worker can work on that state.
     * It includes testing if the worker was already executed.
     *
     * @return bool
     */
    public function canWork(Builder $schema, array $args)
    {
        return isset($args['Language']) && $args['Language'] instanceof Language;
    }
}
