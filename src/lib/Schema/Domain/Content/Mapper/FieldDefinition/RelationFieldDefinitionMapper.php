<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;

class RelationFieldDefinitionMapper extends DecoratingFieldDefinitionMapper implements FieldDefinitionMapper
{
    private NameHelper $nameHelper;

    private ContentTypeService $contentTypeService;

    public function __construct(
        FieldDefinitionMapper $innerMapper,
        NameHelper $nameHelper,
        ContentTypeService $contentTypeService
    ) {
        parent::__construct($innerMapper);
        $this->nameHelper = $nameHelper;
        $this->contentTypeService = $contentTypeService;
    }

    public function mapToFieldValueType(FieldDefinition $fieldDefinition): ?string
    {
        if (!$this->canMap($fieldDefinition)) {
            return parent::mapToFieldValueType($fieldDefinition);
        }
        $settings = $fieldDefinition->getFieldSettings();

        $type = 'Item';
        if (count($settings['selectionContentTypes']) === 1) {
            try {
                $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
                    $settings['selectionContentTypes'][0]
                );
                $type = $this->nameHelper->itemName($contentType);
            } catch (NotFoundException $e) {
                // Nothing to do
            }
        }

        if ($this->isMultiple($fieldDefinition)) {
            $type = 'RelationsConnection';
        }

        return $type;
    }

    public function mapToFieldValueResolver(FieldDefinition $fieldDefinition): ?string
    {
        if (!$this->canMap($fieldDefinition)) {
            return parent::mapToFieldValueResolver($fieldDefinition);
        }

        $isMultiple = $this->isMultiple($fieldDefinition) ? 'true' : 'false';

        return sprintf('@=query("RelationFieldValue", field, %s, args)', $isMultiple);
    }

    protected function canMap(FieldDefinition $fieldDefinition): bool
    {
        return in_array($fieldDefinition->fieldTypeIdentifier, ['ibexa_object_relation', 'ibexa_object_relation_list']);
    }

    public function mapToFieldValueArgsBuilder(FieldDefinition $fieldDefinition): ?string
    {
        if (!$this->canMap($fieldDefinition)) {
            return parent::mapToFieldValueArgsBuilder($fieldDefinition);
        }

        if ($this->isMultiple($fieldDefinition)) {
            return 'Relay::Connection';
        }

        return parent::mapToFieldValueArgsBuilder($fieldDefinition);
    }

    /**
     * Not implemented since we don't use it (canMap is overridden).
     */
    public function getFieldTypeIdentifier(): string
    {
        return '';
    }

    private function isMultiple(FieldDefinition $fieldDefinition): bool
    {
        $constraints = $fieldDefinition->getValidatorConfiguration();

        return isset($constraints['RelationListValueValidator'])
            && $constraints['RelationListValueValidator']['selectionLimit'] !== 1;
    }
}
