<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\BaseWorker;
use Ibexa\GraphQL\Schema\Worker;

class AddItemTypeToItemGroupTypes extends BaseWorker implements Worker
{
    public function work(Builder $schema, array $args)
    {
        $resolve = sprintf(
            '@=resolver("ContentType", [{"identifier": "%s"}])',
            $args['ContentType']->identifier
        );

        $schema->addFieldToType(
            $this->groupTypesName($args),
            new Input\Field(
                $this->typeField($args),
                $this->typeName($args),
                ['resolve' => $resolve]
            )
        );
    }

    public function canWork(Builder $schema, array $args)
    {
        return
            isset($args['ContentType'])
            && $args['ContentType'] instanceof ContentType
            && isset($args['ContentTypeGroup'])
            && $args['ContentTypeGroup'] instanceof ContentTypeGroup
            && !$schema->hasTypeWithField($this->groupTypesName($args), $this->typeField($args));
    }

    protected function typeField(array $args): string
    {
        return $this->getNameHelper()->itemField($args['ContentType']);
    }

    protected function groupTypesName(array $args): string
    {
        return $this->getNameHelper()->itemGroupTypesName($args['ContentTypeGroup']);
    }

    protected function typeName(array $args): string
    {
        return $this->getNameHelper()->itemTypeName($args['ContentType']);
    }
}

class_alias(AddItemTypeToItemGroupTypes::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentType\AddItemTypeToItemGroupTypes');
