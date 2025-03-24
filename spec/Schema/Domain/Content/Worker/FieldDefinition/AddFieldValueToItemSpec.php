<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\FieldDefinition;

use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\Core\Repository\Values\ContentType;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\FieldDefinition\AddFieldValueToItem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Ibexa\GraphQL\Tools\FieldArgument;

class AddFieldValueToItemSpec extends ObjectBehavior
{
    public const FIELDTYPE_IDENTIFIER = 'field';
    public const FIELD_IDENTIFIER = 'test';

    public function let(
        NameHelper $nameHelper,
        FieldDefinitionMapper $mapper
    ): void {
        $this->beConstructedWith($mapper);
        $this->setNameHelper($nameHelper);

        $nameHelper->itemName(Argument::any())->willReturn('TestItemType');
        $nameHelper->fieldDefinitionField(Argument::any())->willReturn('test');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddFieldValueToItem::class);
    }

    public function it_adds_to_the_schema_what_was_returned_by_the_builder(
        Builder $schema,
        FieldDefinitionMapper $mapper
    ): void {
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

    private function buildArguments($fieldTypeIdentifier = self::FIELDTYPE_IDENTIFIER): array
    {
        return [
            'ContentTypeGroup' => new ContentType\ContentTypeGroup(),
            'ContentType' => new ContentType\ContentType(),
            'FieldDefinition' => new ContentType\FieldDefinition([
                'fieldTypeIdentifier' => $fieldTypeIdentifier,
            ]),
        ];
    }
}
