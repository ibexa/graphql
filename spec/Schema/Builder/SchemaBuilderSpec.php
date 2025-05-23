<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Builder;

use Ibexa\GraphQL\Schema\Builder\Input;
use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\GraphQL\Schema\Domain\NameValidator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SchemaBuilderSpec extends ObjectBehavior
{
    public const TYPE = 'Test';
    public const TYPE_TYPE = 'object';

    public const FIELD = 'field';
    public const FIELD_TYPE = 'string';

    public const ARG = 'arg';
    public const ARG_TYPE = 'Boolean';

    public function let(NameValidator $nameValidator): void
    {
        $this->beConstructedWith($nameValidator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SchemaBuilder::class);
    }

    public function it_adds_a_type_to_the_schema(NameValidator $nameValidator): void
    {
        $nameValidator->isValidName(Argument::any())->willReturn(true);

        $this->addType($this->inputType('Parent', 'Interface'));

        $schema = $this->getSchema();

        $schema->shouldHaveGraphQLType();
        $schema->shouldHaveGraphQLTypeThatInherits('Parent');
        $schema->shouldHaveGraphQLTypeThatImplements('Interface');
    }

    public function it_adds_a_field_to_an_existing_type(NameValidator $nameValidator): void
    {
        $nameValidator->isValidName(Argument::any())->willReturn(true);

        $this->addType($this->inputType());
        $this->addFieldToType(
            self::TYPE,
            $this->inputField('Description', '@=query("myresolver")')
        );

        $schema = $this->getSchema();
        $schema->shouldHaveGraphQLType();
        $schema->shouldHaveGraphQLTypeField();
        $schema->shouldHaveGraphQLTypeFieldWithDescription('Description');
        $schema->shouldHaveGraphQLTypeFieldWithResolve('@=query("myresolver")');
    }

    public function it_adds_an_argument_to_an_existing_type_field(NameValidator $nameValidator): void
    {
        $nameValidator->isValidName(Argument::any())->willReturn(true);

        $this->addType($this->inputType());
        $this->addFieldToType(self::TYPE, $this->inputField());
        $this->addArgToField(self::TYPE, self::FIELD, $this->inputArg('Description'));

        $schema = $this->getSchema();
        $schema->shouldHaveGraphQLType();
        $schema->shouldHaveGraphQLTypeField();
        $schema->shouldHaveGraphQLTypeFieldArg();
        $schema->shouldHaveGraphQLTypeFieldArgWithDescription('Description');
    }

    public function getMatchers(): array
    {
        return [
            'haveGraphQLType' => static function (array $schema): bool {
                return
                    isset($schema[self::TYPE]['type'])
                    && $schema[self::TYPE]['type'] === self::TYPE_TYPE;
            },
            'haveGraphQLTypeThatInherits' => static function (array $schema, $inherits): bool {
                return
                    isset($schema[self::TYPE]['inherits'])
                    && in_array($inherits, $schema[self::TYPE]['inherits']);
            },
            'haveGraphQLTypeThatImplements' => static function (array $schema, $interface): bool {
                return
                    isset($schema[self::TYPE]['config']['interfaces'])
                    && in_array($interface, $schema[self::TYPE]['config']['interfaces']);
            },
            'haveGraphQLTypeField' => static function (array $schema): bool {
                return
                    isset($schema[self::TYPE]['config']['fields'][self::FIELD]['type'])
                    && $schema[self::TYPE]['config']['fields'][self::FIELD]['type'] === self::FIELD_TYPE;
            },
            'haveGraphQLTypeFieldWithDescription' => static function (array $schema, $description): bool {
                return
                    isset($schema[self::TYPE]['config']['fields'][self::FIELD]['description'])
                    && $schema[self::TYPE]['config']['fields'][self::FIELD]['description'] === $description;
            },
            'haveGraphQLTypeFieldWithResolve' => static function (array $schema, $resolve): bool {
                return
                    isset($schema[self::TYPE]['config']['fields'][self::FIELD]['description'])
                    && $schema[self::TYPE]['config']['fields'][self::FIELD]['resolve'] === $resolve;
            },
            'haveGraphQLTypeFieldArg' => static function (array $schema): bool {
                return
                    isset($schema[self::TYPE]['config']['fields'][self::FIELD]['args'][self::ARG]['type'])
                    && $schema[self::TYPE]['config']['fields'][self::FIELD]['args'][self::ARG]['type'] === self::ARG_TYPE;
            },
            'haveGraphQLTypeFieldArgWithDescription' => static function (array $schema, $description): bool {
                return
                    isset($schema[self::TYPE]['config']['fields'][self::FIELD]['args'][self::ARG]['description'])
                    && $schema[self::TYPE]['config']['fields'][self::FIELD]['args'][self::ARG]['description'] === $description;
            },
        ];
    }

    protected function inputType($inherits = [], $interfaces = []): Input\Type
    {
        return new Input\Type(
            self::TYPE,
            self::TYPE_TYPE,
            [
                'inherits' => $inherits,
                'interfaces' => $interfaces,
            ]
        );
    }

    protected function inputField($description = null, $resolve = null): Input\Field
    {
        $input = new Input\Field(self::FIELD, self::FIELD_TYPE);

        if (isset($description)) {
            $input->description = $description;
        }

        if (isset($resolve)) {
            $input->resolve = $resolve;
        }

        return $input;
    }

    protected function inputArg($description): Input\Arg
    {
        $input = new Input\Arg(self::ARG, self::ARG_TYPE);

        if (isset($description)) {
            $input->description = $description;
        }

        return $input;
    }
}
