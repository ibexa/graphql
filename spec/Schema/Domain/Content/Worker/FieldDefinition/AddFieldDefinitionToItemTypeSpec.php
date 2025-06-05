<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\FieldDefinition;

use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\Core\Repository\Values\ContentType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\FieldDefinition\AddFieldDefinitionToItemType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Ibexa\GraphQL\Tools\FieldArgument;

class AddFieldDefinitionToItemTypeSpec extends ObjectBehavior
{
    public const TYPE_IDENTIFIER = 'test';
    public const TYPE_NAME = 'TestItemType';
    public const FIELD_IDENTIFIER = 'test_field';
    public const FIELD_NAME = 'testField';
    public const FIELD_DESCRIPTION = ['eng-GB' => 'Description'];
    public const FIELD_TYPE = 'ibexa_string';
    public const FIELD_DEFINITION_TYPE = 'TestFieldDefinition';

    /**
     * @var \Ibexa\Core\Repository\Values\ContentType\FieldDefinition
     */
    private $defaultFieldDefinition;

    public function __construct()
    {
        $this->defaultFieldDefinition = $this->buildFieldDefinition(self::FIELD_TYPE);
    }

    public function let(FieldDefinitionMapper $mapper, NameHelper $nameHelper): void
    {
        $this->beConstructedWith($mapper);

        $nameHelper
            ->itemtypeName(
                Argument::type(ContentType\ContentType::class)
            )
            ->willReturn(self::TYPE_NAME);

        $nameHelper
            ->fieldDefinitionField(
                Argument::type(FieldDefinition::class)
            )
            ->willReturn(self::FIELD_NAME);

        $this->setNameHelper($nameHelper);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddFieldDefinitionToItemType::class);
    }

    public function it_adds_the_field_definition_to_the_domain_content_type(Builder $schema): void
    {
        $this->work($schema, $this->buildArguments());

        $schema->addFieldToType(
            self::TYPE_NAME,
            FieldArgument::hasName(self::FIELD_NAME)
        )->shouldHaveBeenCalled();
    }

    public function it_uses_the_FieldDefinition_description_as_the_Field_description(Builder $schema): void
    {
        $this->work($schema, $this->buildArguments());

        $schema->addFieldToType(
            self::TYPE_NAME,
            FieldArgument::hasDescription(self::FIELD_DESCRIPTION['eng-GB'])
        )->shouldHaveBeenCalled();
    }

    public function it_gets_the_FieldDefinition_type_from_the_FieldDefinitionMapper(Builder $schema, FieldDefinitionMapper $mapper): void
    {
        $mapper->mapToFieldDefinitionType(Argument::any())->willReturn(self::FIELD_DEFINITION_TYPE);
        $schema->addFieldToType(
            self::TYPE_NAME,
            FieldArgument::hasType(self::FIELD_DEFINITION_TYPE)
        )->shouldBeCalled();

        $this->work($schema, $this->buildArguments());
    }

    protected function buildArguments($fieldTypeIdentifier = null): array
    {
        $return = [
            'ContentTypeGroup' => new ContentType\ContentTypeGroup(),
            'ContentType' => new ContentType\ContentType(['identifier' => self::TYPE_IDENTIFIER]),
            'FieldDefinition' => $fieldTypeIdentifier ? $this->buildFieldDefinition($fieldTypeIdentifier) : $this->defaultFieldDefinition,
        ];

        return $return;
    }

    private function buildFieldDefinition($fieldTypeIdentifier): FieldDefinition
    {
        return new FieldDefinition([
            'identifier' => self::FIELD_IDENTIFIER,
            'descriptions' => self::FIELD_DESCRIPTION,
            'fieldTypeIdentifier' => $fieldTypeIdentifier,
        ]);
    }
}
