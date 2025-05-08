<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\SelectionFieldDefinitionMapper;
use PhpSpec\ObjectBehavior;

class SelectionFieldDefinitionMapperSpec extends ObjectBehavior
{
    public const FIELD_IDENTIFIER = 'test';
    public const TYPE_IDENTIFIER = 'ezselection';

    public function let(FieldDefinitionMapper $innerMapper): void
    {
        $this->beConstructedWith($innerMapper);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SelectionFieldDefinitionMapper::class);
        $this->shouldHaveType(FieldDefinitionMapper::class);
    }

    public function it_maps_to_an_array_of_strings_if_multiple_is_set_to_true(): void
    {
        $this->mapToFieldValueType($this->createMultiFieldDefinition())->shouldReturn('[String]');
    }

    public function it_maps_to_a_string_if_multiple_is_set_to_false(): void
    {
        $this->mapToFieldValueType($this->createSingleFieldDefinition())->shouldReturn('String');
    }

    private function createSingleFieldDefinition()
    {
        return $this->createFieldDefinition(['isMultiple' => false]);
    }

    private function createMultiFieldDefinition()
    {
        return $this->createFieldDefinition(['isMultiple' => true]);
    }

    private function createFieldDefinition(array $options): FieldDefinition
    {
        return new FieldDefinition([
            'identifier' => self::FIELD_IDENTIFIER,
            'fieldTypeIdentifier' => self::TYPE_IDENTIFIER,
            'fieldSettings' => [
                'isMultiple' => $options['isMultiple'] ?? false,
            ],
        ]);
    }
}
