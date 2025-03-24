<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\ConfigurableFieldDefinitionMapper;
use PhpSpec\ObjectBehavior;

class ConfigurableFieldDefinitionMapperSpec extends ObjectBehavior
{
    public const FIELD_IDENTIFIER = 'test';
    public const CONFIG = [
        'configured_type' => [
            'value_type' => self::VALUE_TYPE,
            'definition_type' => self::DEFINITION_TYPE,
            'value_resolver' => self::VALUE_RESOLVER,
        ],
    ];

    public const VALUE_TYPE = 'ConfiguredFieldValue';
    public const VALUE_RESOLVER = 'valueResolver';
    public const DEFINITION_TYPE = 'ConfiguredFieldDefinition';

    public function let(FieldDefinitionMapper $innerMapper): void
    {
        $this->beConstructedWith($innerMapper, self::CONFIG);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ConfigurableFieldDefinitionMapper::class);
        $this->shouldHaveType(FieldDefinitionMapper::class);
    }

    public function it_calls_the_inner_mapper_if_it_does_not_have_a_value_type_for_a_field_type_identifier(FieldDefinitionMapper $innerMapper): void
    {
        $fieldDefinition = $this->createUnconfiguredFieldDefinition();

        $this->mapToFieldValueType($fieldDefinition)->shouldBeNull();
        $innerMapper->mapToFieldValueType($fieldDefinition)->shouldHaveBeenCalled();
    }

    public function it_returns_the_value_type_for_a_configured_field_type_identifier(FieldDefinitionMapper $innerMapper): void
    {
        $fieldDefinition = $this->createConfiguredFieldDefinition();

        $this->mapToFieldValueType($fieldDefinition)->shouldReturn(self::VALUE_TYPE);
        $innerMapper->mapToFieldValueType($fieldDefinition)->shouldNotHaveBeenCalled();
    }

    public function it_calls_the_inner_mapper_if_it_does_not_have_a_definition_type_for_a_field_type_identifier(FieldDefinitionMapper $innerMapper): void
    {
        $fieldDefinition = $this->createUnconfiguredFieldDefinition();

        $this->mapToFieldDefinitionType($fieldDefinition)->shouldBeNull();
        $innerMapper->mapToFieldDefinitionType($fieldDefinition)->shouldHaveBeenCalled();
    }

    public function it_returns_the_definition_type_for_a_configured_field_type_identifier(FieldDefinitionMapper $innerMapper): void
    {
        $fieldDefinition = $this->createConfiguredFieldDefinition();

        $this->mapToFieldDefinitionType($fieldDefinition)->shouldReturn(self::DEFINITION_TYPE);
        $innerMapper->mapToFieldDefinitionType($fieldDefinition)->shouldNotHaveBeenCalled();
    }

    public function it_calls_the_inner_mapper_if_it_does_not_have_a_value_resolver_for_a_field_type_identifier(FieldDefinitionMapper $innerMapper): void
    {
        $fieldDefinition = $this->createUnconfiguredFieldDefinition();

        $this->mapToFieldDefinitionType($fieldDefinition)->shouldBeNull();
        $innerMapper->mapToFieldDefinitionType($fieldDefinition)->shouldHaveBeenCalled();
    }

    public function it_returns_the_completed_value_resolver_for_a_configured_field_type_identifier(FieldDefinitionMapper $innerMapper): void
    {
        $fieldDefinition = $this->createConfiguredFieldDefinition();

        $this->mapToFieldValueResolver($fieldDefinition)->shouldReturn('@=' . self::VALUE_RESOLVER);
        $innerMapper->mapToFieldValueResolver($fieldDefinition)->shouldNotHaveBeenCalled();
    }

    /**
     * @return \Ibexa\Core\Repository\Values\ContentType\FieldDefinition
     */
    protected function createConfiguredFieldDefinition(): FieldDefinition
    {
        return new FieldDefinition([
            'identifier' => 'test',
            'fieldTypeIdentifier' => 'configured_type',
        ]);
    }

    /**
     * @return \Ibexa\Core\Repository\Values\ContentType\FieldDefinition
     */
    protected function createUnconfiguredFieldDefinition(): FieldDefinition
    {
        return new FieldDefinition([
            'identifier' => 'test',
            'fieldTypeIdentifier' => 'unconfigured_type',
        ]);
    }
}
