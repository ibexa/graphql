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
    private ContentTypeService $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param array{ContentTypeGroup: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup} $args
     */
    public function work(Builder $schema, array $args): void
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

    /**
     * @param array{ContentTypeGroup?: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup|mixed} $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args['ContentTypeGroup'])
            && $args['ContentTypeGroup'] instanceof ContentTypeGroup
            && !$schema->hasType($this->typeName($args))
            && !empty($this->contentTypeService->loadContentTypes($args['ContentTypeGroup']));
    }

    /**
     * @param array{ContentTypeGroup: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup} $args
     */
    protected function typeName(array $args): string
    {
        return $this->getNameHelper()->itemGroupName($args['ContentTypeGroup']);
    }

    /**
     * @param array{ContentTypeGroup: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup} $args
     */
    private function groupTypesName(array $args): string
    {
        return $this->getNameHelper()->itemGroupTypesName($args['ContentTypeGroup']);
    }
}
