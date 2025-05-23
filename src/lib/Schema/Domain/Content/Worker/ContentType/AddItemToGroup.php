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

class AddItemToGroup extends BaseWorker implements Worker
{
    /**
     * @param array{ContentType: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType, ContentTypeGroup: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup} $args
     */
    public function work(Builder $schema, array $args): void
    {
        $contentType = $args['ContentType'];
        $descriptions = $contentType->getDescriptions();

        $schema->addFieldToType($this->groupName($args), new Input\Field(
            $this->typeField($args),
            $this->typeName($args),
            [
                'description' => isset($descriptions['eng-GB']) ? $descriptions['eng-GB'] : 'No description available',
                'resolve' => sprintf('@=query("ItemOfType", args, "%s")', $contentType->identifier),
            ]
        ));

        $schema->addArgToField($this->groupName($args), $this->typeField($args), new Input\Arg(
            'contentId',
            'Int',
            ['description' => sprintf('Content ID of the %s', $contentType->identifier)]
        ));

        $schema->addArgToField($this->groupName($args), $this->typeField($args), new Input\Arg(
            'remoteId',
            'String',
            ['description' => sprintf('Content remote ID of the %s', $contentType->identifier)]
        ));

        $schema->addArgToField($this->groupName($args), $this->typeField($args), new Input\Arg(
            'locationId',
            'Int',
            ['description' => sprintf('Location ID of the %s', $contentType->identifier)]
        ));

        $schema->addArgToField($this->groupName($args), $this->typeField($args), new Input\Arg(
            'locationRemoteId',
            'String',
            ['description' => sprintf('Location remote ID of the %s', $contentType->identifier)]
        ));

        $schema->addArgToField($this->groupName($args), $this->typeField($args), new Input\Arg(
            'urlAlias',
            'String',
            ['description' => sprintf('URL alias of the %s', $contentType->identifier)]
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
            && !$schema->hasTypeWithField($this->groupName($args), $this->typeField($args));
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
    protected function typeField(array $args): string
    {
        return $this->getNameHelper()->itemField($args['ContentType']);
    }

    /**
     * @param array{ContentType: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType} $args
     */
    protected function typeName(array $args): string
    {
        return $this->getNameHelper()->itemName($args['ContentType']);
    }
}
