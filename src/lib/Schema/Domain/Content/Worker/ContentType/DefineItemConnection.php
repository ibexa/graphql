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
use Ibexa\GraphQL\Schema\Worker;

class DefineItemConnection extends BaseWorker implements Worker
{
    /**
     * @param array{ContentType: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType} $args
     */
    public function work(Builder $schema, array $args): void
    {
        $schema->addType(new Input\Type(
            $this->connectionTypeName($args),
            'relay-connection',
            [
                'nodeType' => $this->typeName($args),
                'connectionFields' => [
                    'sliceSize' => ['type' => 'Int!'],
                    'orderBy' => ['type' => 'String'],
                ],
            ]
        ));
    }

    /**
     * @param array{ContentType?: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType|mixed} $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return isset($args['ContentType']) && $args['ContentType'] instanceof ContentType
               && !$schema->hasType($this->connectionTypeName($args));
    }

    /**
     * @param array{ContentType: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType} $args
     */
    protected function connectionTypeName(array $args): string
    {
        return $this->getNameHelper()->itemConnectionName($args['ContentType']);
    }

    /**
     * @param array{ContentType: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType} $args
     */
    protected function typeName(array $args): string
    {
        return $this->getNameHelper()->itemName($args['ContentType']);
    }
}
