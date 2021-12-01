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
        $contentTypeGroup = $args['ContentTypeGroup'];
        $schema->addFieldToType('Domain', new Input\Field(
            $this->fieldName($args),
            $this->typeGroupName($args),
            [
                'description' => $contentTypeGroup->getDescription('eng-GB'),
                'resolve' => sprintf(
                    '@=resolver("ContentTypeGroupByIdentifier", ["%s"])',
                    $contentTypeGroup->identifier
                ),
            ]
        ));
    }

    public function canWork(Builder $schema, array $args)
    {
        return
            isset($args['ContentTypeGroup'])
            && $args['ContentTypeGroup'] instanceof ContentTypeGroup
            && !$schema->hasTypeWithField('Domain', $this->fieldName($args))
            && !empty($this->contentTypeService->loadContentTypes($args['ContentTypeGroup']));
    }

    private function fieldName($args): string
    {
        return $this->getNameHelper()->domainGroupField($args['ContentTypeGroup']);
    }

    private function typeGroupName($args): string
    {
        return $this->getNameHelper()->domainGroupName($args['ContentTypeGroup']);
    }
}

class_alias(AddDomainGroupToDomain::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\AddDomainGroupToDomain');
