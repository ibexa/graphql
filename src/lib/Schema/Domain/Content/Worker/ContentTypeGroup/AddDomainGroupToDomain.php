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

final class AddDomainGroupToDomain extends BaseWorker implements Worker
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
        $contentTypeGroup = $args['ContentTypeGroup'];
        $schema->addFieldToType('Domain', new Input\Field(
            $this->fieldName($args),
            $this->typeGroupName($args),
            [
                'description' => $contentTypeGroup->getDescription('eng-GB'),
                'resolve' => sprintf(
                    '@=query("ContentTypeGroupByIdentifier", "%s")',
                    $contentTypeGroup->identifier
                ),
            ]
        ));
    }

    /**
     * @param array{ContentTypeGroup?: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup|mixed} $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args['ContentTypeGroup'])
            && $args['ContentTypeGroup'] instanceof ContentTypeGroup
            && !$schema->hasTypeWithField('Domain', $this->fieldName($args))
            && !empty($this->contentTypeService->loadContentTypes($args['ContentTypeGroup']));
    }

    /**
     * @param array{ContentTypeGroup: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup} $args
     */
    private function fieldName(array $args): string
    {
        return $this->getNameHelper()->itemGroupField($args['ContentTypeGroup']);
    }

    /**
     * @param array{ContentTypeGroup: \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup} $args
     */
    private function typeGroupName(array $args): string
    {
        return $this->getNameHelper()->itemGroupName($args['ContentTypeGroup']);
    }
}
