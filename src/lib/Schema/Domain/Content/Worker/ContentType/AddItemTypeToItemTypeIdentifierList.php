<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\BaseWorker;
use Ibexa\GraphQL\Schema\Initializer;
use Ibexa\GraphQL\Schema\Worker;

/**
 * Adds a content type to the content type identifiers list (ContentTypeIdentifier).
 */
class AddItemTypeToItemTypeIdentifierList extends BaseWorker implements Worker, Initializer
{
    public const TYPE = 'ContentTypeIdentifier';

    public function work(Builder $schema, array $args)
    {
        $contentType = $args['ContentType'];

        $descriptions = $contentType->getDescriptions();
        $description = isset($descriptions['eng-GB']) ? $descriptions['eng-GB'] : 'No description available';

        $schema->addValueToEnum(
            self::TYPE,
            new Input\EnumValue(
                $contentType->identifier,
                ['description' => $description]
            )
        );
    }

    public function init(Builder $schema)
    {
        $schema->addType(new Input\Type(self::TYPE, 'enum'));
    }

    public function canWork(Builder $schema, array $args)
    {
        $canWork =
            isset($args['ContentType'])
            && $args['ContentType'] instanceof ContentType
            && $schema->hasType(self::TYPE);

        return $canWork;
    }
}

class_alias(AddItemTypeToItemTypeIdentifierList::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentType\AddItemTypeToItemTypeIdentifierList');
