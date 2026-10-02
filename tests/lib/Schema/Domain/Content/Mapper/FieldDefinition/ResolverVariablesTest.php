<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\ResolverVariables;
use PHPUnit\Framework\TestCase;

final class ResolverVariablesTest extends TestCase
{
    /**
     * @dataProvider provideResolvers
     */
    public function testMapToFieldValueResolver(
        string $fieldDefinitionIdentifier,
        string $innerResolver,
        string $expectedResolver
    ): void {
        $innerMapper = $this->createStub(FieldDefinitionMapper::class);
        $innerMapper->method('mapToFieldValueResolver')->willReturn($innerResolver);

        $mapper = new ResolverVariables($innerMapper);

        self::assertSame(
            $expectedResolver,
            $mapper->mapToFieldValueResolver(new FieldDefinition(['identifier' => $fieldDefinitionIdentifier]))
        );
    }

    public function testMapToFieldValueResolverKeepsNullFromInnerMapper(): void
    {
        $innerMapper = $this->createStub(FieldDefinitionMapper::class);
        $innerMapper->method('mapToFieldValueResolver')->willReturn(null);

        $mapper = new ResolverVariables($innerMapper);

        self::assertNull($mapper->mapToFieldValueResolver(new FieldDefinition(['identifier' => 'title'])));
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function provideResolvers(): iterable
    {
        yield 'identifier containing "content" is not rewritten' => [
            'content_matrix',
            '@=query("MatrixFieldValue", value, "content_matrix")',
            '@=query("MatrixFieldValue", value, "content_matrix")',
        ];

        yield 'identifier containing "location" is not rewritten' => [
            'location_data',
            '@=query("MatrixFieldValue", value, "location_data")',
            '@=query("MatrixFieldValue", value, "location_data")',
        ];

        yield 'identifier containing "item" is not rewritten' => [
            'item_list',
            '@=query("MatrixFieldValue", value, "item_list")',
            '@=query("MatrixFieldValue", value, "item_list")',
        ];

        yield 'identifier containing "field" is not rewritten' => [
            'field_6216205b32553',
            '@=query("MatrixFieldValue", value, "field_6216205b32553")',
            '@=query("MatrixFieldValue", value, "field_6216205b32553")',
        ];

        yield 'bare field variable is resolved with the field identifier' => [
            'content_title',
            '@=field',
            '@=query("ItemFieldValue", value, "content_title", args)',
        ];

        yield 'content and field variables are replaced' => [
            'title',
            '@=query("SelectionFieldValue", field, content)',
            '@=query("SelectionFieldValue", query("ItemFieldValue", value, "title", args), value.getContent())',
        ];

        yield 'single quoted identifier is not rewritten' => [
            'content_matrix',
            "@=query('MatrixFieldValue', value, 'content_matrix')",
            "@=query('MatrixFieldValue', value, 'content_matrix')",
        ];

        yield 'single quoted string is skipped, field variable is replaced' => [
            'title',
            "@=query('X', field)",
            "@=query('X', query(\"ItemFieldValue\", value, \"title\", args))",
        ];

        yield 'escaped quote does not end the string' => [
            'title',
            '@=query("X", "a\\"content", field)',
            '@=query("X", "a\\"content", query("ItemFieldValue", value, "title", args))',
        ];

        yield 'name after a dot is a member, not a variable' => [
            'title',
            '@=field.location',
            '@=query("ItemFieldValue", value, "title", args).location',
        ];

        yield 'very long string literal' => [
            'title',
            '@=query("X", "' . str_repeat('a', 20000) . '", field)',
            '@=query("X", "' . str_repeat('a', 20000) . '", query("ItemFieldValue", value, "title", args))',
        ];

        yield 'location and item variables are replaced' => [
            'title',
            '@=query("Custom", location, item)',
            '@=query("Custom", value.getLocation(), value)',
        ];
    }
}
