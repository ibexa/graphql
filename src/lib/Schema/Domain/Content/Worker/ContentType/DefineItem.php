<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\BaseWorker;
use Ibexa\GraphQL\Schema\Worker;

class DefineItem extends BaseWorker implements Worker
{
    public function work(Builder $schema, array $args)
    {
        $schema->addType(new Input\Type(
            $this->typeName($args), 'object',
            [
                'inherits' => 'AbstractItem',
                'interfaces' => ['Item', 'Node'],
            ]
        ));
    }

    public function canWork(Builder $schema, array $args)
    {
        return
            isset($args['ContentType'])
            && $args['ContentType'] instanceof ContentType
            && !$schema->hasType($this->typeName($args));
    }

    protected function typeName(array $args): string
    {
        return $this->getNameHelper()->itemName($args['ContentType']);
    }
}

class_alias(DefineItem::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentType\DefineItem');