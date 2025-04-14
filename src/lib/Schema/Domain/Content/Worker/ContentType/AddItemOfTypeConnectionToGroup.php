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
    /**
     * @param array{ContentType: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType, ContentTypeGroup: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup} $args
     */
    public function work(Builder $schema, array $args): void
    {
        $contentType = $args['ContentType'];
        $descriptions = $contentType->getDescriptions();

        $schema->addFieldToType($this->groupName($args), new Input\Field(
            $this->connectionField($args),
            $this->connectionType($args),
            [
                'description' => isset($descriptions['eng-GB']) ? $descriptions['eng-GB'] : 'No description available',
                'resolve' => sprintf(
                    '@=query("ItemsOfTypeAsConnection", "%s", args)',
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
    /**
     * @param array{ContentType?: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType|mixed, ContentTypeGroup?: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup|mixed} $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args['ContentType'])
            && $args['ContentType'] instanceof ContentType
            && isset($args['ContentTypeGroup'])
            && $args['ContentTypeGroup'] instanceof ContentTypeGroup
            && !$schema->hasTypeWithField($this->groupName($args), $this->connectionField($args));
    }

    /**
     * @param array{ContentTypeGroup: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup} $args
     */
    protected function groupName(array $args): string
    {
        return $this->getNameHelper()->itemGroupName($args['ContentTypeGroup']);
    }

    /**
     * @param array{ContentType: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType} $args
     */
    protected function connectionField(array $args): string
    {
        return $this->getNameHelper()->itemConnectionField($args['ContentType']);
    }

    /**
     * @param array{ContentType: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType} $args
     */
    protected function connectionType(array $args): string
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
