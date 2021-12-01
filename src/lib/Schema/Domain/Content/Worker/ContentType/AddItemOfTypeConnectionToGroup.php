<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\BaseWorker;
use Ibexa\GraphQL\Schema\Worker;

class AddItemOfTypeConnectionToGroup extends BaseWorker implements Worker
{
    public function work(Builder $schema, array $args)
    {
        $contentType = $args['ContentType'];
        $descriptions = $contentType->getDescriptions();

        $schema->addFieldToType($this->groupName($args), new Input\Field(
            $this->connectionField($args),
            $this->connectionType($args),
            [
                'description' => isset($descriptions['eng-GB']) ? $descriptions['eng-GB'] : 'No description available',
                'resolve' => sprintf(
                    '@=resolver("ItemsOfTypeAsConnection", ["%s", args])',
                    $contentType->identifier
                ),
                'argsBuilder' => 'Relay::Connection',
            ]
        ));

        $schema->addArgToField($this->groupName($args), $this->connectionField($args), new Input\Arg(
            'query',
            'ContentSearchQuery',
            ['description' => 'A Content query used to filter results']
        ));

        $schema->addArgToField($this->groupName($args), $this->connectionField($args), new Input\Arg(
            'sortBy',
            '[SortByOptions]',
            ['description' => 'A Sort Clause, or array of Clauses. Add _desc after a Clause to reverse it']
        ));
    }

    public function canWork(Builder $schema, array $args)
    {
        return
            isset($args['ContentType'])
            && $args['ContentType'] instanceof ContentType
            && isset($args['ContentTypeGroup'])
            && $args['ContentTypeGroup'] instanceof ContentTypeGroup
            && !$schema->hasTypeWithField($this->groupName($args), $this->connectionField($args));
    }

    protected function groupName(array $args): string
    {
        return $this->getNameHelper()->itemGroupName($args['ContentTypeGroup']);
    }

    protected function connectionField(array $args): string
    {
        return $this->getNameHelper()->itemConnectionField($args['ContentType']);
    }

    protected function connectionType(array $args): string
    {
        return $this->getNameHelper()->itemConnectionName($args['ContentType']);
    }

    protected function typeName($args): string
    {
        return $this->getNameHelper()->itemName($args['ContentType']);
    }
}

class_alias(AddItemOfTypeConnectionToGroup::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentType\AddItemOfTypeConnectionToGroup');
