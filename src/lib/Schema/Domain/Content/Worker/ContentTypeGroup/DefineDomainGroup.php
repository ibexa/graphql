<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\BaseWorker;
use Ibexa\GraphQL\Schema\Worker;

class DefineDomainGroup extends BaseWorker implements Worker
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function work(Builder $schema, array $args)
    {
        $schema->addType(new Input\Type(
            $this->typeName($args),
            'object',
            ['inherits' => 'DomainContentTypeGroup']
        ));

        $schema->addFieldToType(
            $this->typeName($args),
            new Input\Field(
                '_types',
                $this->groupTypesName($args),
                ['resolve' => []]
            )
        );
    }

    public function canWork(Builder $schema, array $args)
    {
        return
            isset($args['ContentTypeGroup'])
            && $args['ContentTypeGroup'] instanceof ContentTypeGroup
            && !$schema->hasType($this->typeName($args))
            && !empty($this->contentTypeService->loadContentTypes($args['ContentTypeGroup']));
    }

    protected function typeName($args): string
    {
        return $this->getNameHelper()->itemGroupName($args['ContentTypeGroup']);
    }

    private function groupTypesName(array $args): string
    {
        return $this->getNameHelper()->itemGroupTypesName($args['ContentTypeGroup']);
    }
}

class_alias(DefineDomainGroup::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\DefineDomainGroup');
