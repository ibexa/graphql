<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Domain\Content;

use Generator;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input;
use Ibexa\GraphQL\Schema\Domain\Iterator;
use Ibexa\GraphQL\Schema\Domain\NameValidator;

class ContentDomainIterator implements Iterator
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\GraphQL\Schema\Domain\NameValidator */
    private $nameValidator;

    public function __construct(
        ContentTypeService $contentTypeService,
        NameValidator $nameValidator
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->nameValidator = $nameValidator;
    }

    public function init(Builder $schema)
    {
        $schema->addType(
            new Input\Type('Domain', 'object', ['inherits' => ['Platform']])
        );
    }

    public function iterate(): Generator
    {
        foreach ($this->contentTypeService->loadContentTypeGroups() as $contentTypeGroup) {
            yield ['ContentTypeGroup' => $contentTypeGroup];

            foreach ($this->contentTypeService->loadContentTypes($contentTypeGroup) as $contentType) {
                if (!$this->nameValidator->isValidName($contentType->identifier)) {
                    $this->nameValidator->generateInvalidNameWarning('Content type', $contentType->identifier);

                    continue;
                }

                yield ['ContentTypeGroup' => $contentTypeGroup]
                    + ['ContentType' => $contentType];

                foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
                    yield ['ContentTypeGroup' => $contentTypeGroup]
                        + ['ContentType' => $contentType]
                        + ['FieldDefinition' => $fieldDefinition];
                }
            }
        }
    }
}

class_alias(ContentDomainIterator::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\ContentDomainIterator');
