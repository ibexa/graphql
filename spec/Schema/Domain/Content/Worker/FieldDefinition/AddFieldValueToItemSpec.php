<?php

namespace Ibexa\Spec\GraphQL\Schema\Domain\Content\Worker\FieldDefinition;

use Ibexa\GraphQL\Schema\Domain\Content\FieldValueBuilder\FieldValueBuilder;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\FieldDefinition\AddFieldValueToItem;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\Spec\GraphQL\Tools\FieldArgument;
use Ibexa\Core\Repository\Values\ContentType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AddFieldValueToItemSpec extends ObjectBehavior
{
    const FIELDTYPE_IDENTIFIER = 'field';
    const FIELD_IDENTIFIER = 'test';

    function let(
        NameHelper $nameHelper,
        FieldDefinitionMapper $mapper
    )
    {
        $this->beConstructedWith($mapper);
        $this->setNameHelper($nameHelper);

        $nameHelper->itemName(Argument::any())->willReturn('TestItemType');
        $nameHelper->fieldDefinitionField(Argument::any())->willReturn('test');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddFieldValueToItem::class);
    }
    
    function it_adds_to_the_schema_what_was_returned_by_the_builder(
        Builder $schema,
        FieldDefinitionMapper $mapper
    )
    {
        $mapper->mapToFieldValueType(Argument::any())->willReturn('String');
        $mapper->mapToFieldValueResolver(Argument::any())->willReturn('field');
        $mapper->mapToFieldValueArgsBuilder(Argument::any())->willReturn(null);

        $schema->addFieldToType(
            Argument::any(),
            Argument::allOf(
                FieldArgument::hasName(self::FIELD_IDENTIFIER),
                FieldArgument::hasType('String'),
                FieldArgument::withResolver('field')
            )
        );
        $this->work($schema, $this->buildArguments());
    }

    private function buildArguments($fieldTypeIdentifier = self::FIELDTYPE_IDENTIFIER)
    {
        return [
            'ContentTypeGroup' => new ContentType\ContentTypeGroup(),
            'ContentType' => new ContentType\ContentType(),
            'FieldDefinition' => new ContentType\FieldDefinition([
                'fieldTypeIdentifier' => $fieldTypeIdentifier
            ]),
        ];
    }
}

class_alias(AddFieldValueToItemSpec::class, 'spec\EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\FieldDefinition\AddFieldValueToItemSpec');
