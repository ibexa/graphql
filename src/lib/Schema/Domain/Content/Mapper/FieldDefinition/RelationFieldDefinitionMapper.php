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

    private bool $enablePagination;

    public function __construct(
        FieldDefinitionMapper $innerMapper,
        NameHelper $nameHelper,
        ContentTypeService $contentTypeService,
        bool $enablePagination
    ) {
        parent::__construct($innerMapper);
        $this->nameHelper = $nameHelper;
        $this->contentTypeService = $contentTypeService;
        $this->enablePagination = $enablePagination;
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
            if ($this->enablePagination) {
                $type = 'RelationsConnection';
            } else {
                @trigger_error(
                    'Disable pagination for ezobjectrelationlist has been deprecated since version 4.6 ' .
                    'and will be removed in version 5.0. To start receiving `RelationsConnection` instead of the deprecated ' .
                    '`[' . $type . ']`, set the parameter `ibexa.graphql.schema.ibexa_object_relation_list.enable_pagination` to `true`.',
                    E_USER_DEPRECATED
                );

                $type = "[$type]";
            }
        }

        return $type;
    }

    public function mapToFieldValueResolver(FieldDefinition $fieldDefinition): ?string
    {
        if (!$this->canMap($fieldDefinition)) {
            return parent::mapToFieldValueResolver($fieldDefinition);
        }

        $isMultiple = $this->isMultiple($fieldDefinition) ? 'true' : 'false';

        return sprintf('@=resolver("RelationFieldValue", [field, %s, args])', $isMultiple);
    }

    protected function canMap(FieldDefinition $fieldDefinition)
    {
        return in_array($fieldDefinition->fieldTypeIdentifier, ['ezobjectrelation', 'ezobjectrelationlist']);
    }

    public function mapToFieldValueArgsBuilder(FieldDefinition $fieldDefinition): ?string
    {
        if (!$this->canMap($fieldDefinition)) {
            return parent::mapToFieldValueArgsBuilder($fieldDefinition);
        }

        if ($this->isMultiple($fieldDefinition) && $this->enablePagination) {
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

    private function isMultiple(FieldDefinition $fieldDefinition)
    {
        $constraints = $fieldDefinition->getValidatorConfiguration();

        return isset($constraints['RelationListValueValidator'])
            && $constraints['RelationListValueValidator']['selectionLimit'] !== 1;
    }
}

class_alias(RelationFieldDefinitionMapper::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Mapper\FieldDefinition\RelationFieldDefinitionMapper');
