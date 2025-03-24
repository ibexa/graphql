<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\RelationFieldDefinitionMapper;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use PhpSpec\ObjectBehavior;

class RelationFieldDefinitionMapperSpec extends ObjectBehavior
{
    public const DEF_LIMIT_SINGLE = 1;
    public const DEF_LIMIT_MULTI = 5;
    public const DEF_LIMIT_NONE = 0;

    public function let(NameHelper $nameHelper, ContentTypeService $contentTypeService, FieldDefinitionMapper $innerMapper): void
    {
        $this->beConstructedWith($innerMapper, $nameHelper, $contentTypeService);

        $articleContentType = new ContentType(['identifier' => 'article']);
        $folderContentType = new ContentType(['identifier' => 'folder']);
        $contentTypeService->loadContentTypeByIdentifier('article')->willReturn($articleContentType);
        $contentTypeService->loadContentTypeByIdentifier('folder')->willReturn($folderContentType);

        $nameHelper->itemName($articleContentType)->willReturn('ArticleItem');
        $nameHelper->itemName($folderContentType)->willReturn('FolderItem');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RelationFieldDefinitionMapper::class);
        $this->shouldHaveType(FieldDefinitionMapper::class);
    }

    public function it_maps_single_selection_without_type_limitations_to_a_single_generic_content(): void
    {
        $fieldDefinition = $this->createFieldDefinition(self::DEF_LIMIT_SINGLE, []);
        $this->mapToFieldValueType($fieldDefinition)->shouldReturn('Item');
    }

    public function it_maps_single_selection_with_multiple_type_limitations_to_a_single_generic_content(): void
    {
        $fieldDefinition = $this->createFieldDefinition(self::DEF_LIMIT_SINGLE, ['article', 'blog_post']);
        $this->mapToFieldValueType($fieldDefinition)->shouldReturn('Item');
    }

    public function it_maps_single_selection_with_a_unique_type_limitations_to_a_single_item_of_that_type(): void
    {
        $fieldDefinition = $this->createFieldDefinition(self::DEF_LIMIT_SINGLE, ['article']);
        $this->mapToFieldValueType($fieldDefinition)->shouldReturn('ArticleItem');
    }

    public function it_maps_multi_selection_without_type_limitations_to_an_array_of_generic_content(): void
    {
        $fieldDefinition = $this->createFieldDefinition(self::DEF_LIMIT_MULTI, []);
        $this->mapToFieldValueType($fieldDefinition)->shouldReturn('[Item]');
    }

    public function it_maps_multi_selection_with_multiple_type_limitations_to_an_array_of_generic_content(): void
    {
        $fieldDefinition = $this->createFieldDefinition(self::DEF_LIMIT_NONE, ['article', 'blog_post']);
        $this->mapToFieldValueType($fieldDefinition)->shouldReturn('[Item]');
    }

    public function it_maps_multi_selection_with_a_unique_type_limitations_to_an_array_of_that_type(): void
    {
        $fieldDefinition = $this->createFieldDefinition(self::DEF_LIMIT_MULTI, ['article']);
        $this->mapToFieldValueType($fieldDefinition)->shouldReturn('[ArticleItem]');
    }

    public function it_delegates_the_field_definition_type_to_the_inner_mapper(FieldDefinitionMapper $innerMapper): void
    {
        $fieldDefinition = $this->createFieldDefinition();
        $innerMapper->mapToFieldDefinitionType($fieldDefinition)->willReturn('SomeFieldDefinition');
        $this->mapToFieldDefinitionType($fieldDefinition)->shouldReturn('SomeFieldDefinition');
    }

    public function it_maps_multi_selection_to_resolve_multiple(): void
    {
        $fieldDefinition = $this->createFieldDefinition(self::DEF_LIMIT_MULTI);
        $this->mapToFieldValueResolver($fieldDefinition)->shouldReturn('@=query("RelationFieldValue", field, true)');
    }

    public function it_maps_single_selection_to_resolve_single(): void
    {
        $fieldDefinition = $this->createFieldDefinition(self::DEF_LIMIT_SINGLE);
        $this->mapToFieldValueResolver($fieldDefinition)->shouldReturn('@=query("RelationFieldValue", field, false)');
    }

    private function createFieldDefinition(int $selectionLimit = 0, array $selectionContentTypes = []): FieldDefinition
    {
        return new FieldDefinition([
            'fieldTypeIdentifier' => 'ezobjectrelationlist',
            'fieldSettings' => [
                'selectionContentTypes' => $selectionContentTypes,
            ],
            'validatorConfiguration' => [
                'RelationListValueValidator' => ['selectionLimit' => $selectionLimit],
            ],
        ]);
    }
}
