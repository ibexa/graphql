<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;
use UnexpectedValueException;

/**
 * Maps a Field Definition to its GraphQL components.
 */
class ResolverVariables implements FieldDefinitionMapper
{
    /**
     * @var \Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper
     */
    private $innerMapper;

    public function __construct(FieldDefinitionMapper $innerMapper)
    {
        $this->innerMapper = $innerMapper;
    }

    public function mapToFieldDefinitionType(FieldDefinition $fieldDefinition): string
    {
        return $this->innerMapper->mapToFieldDefinitionType($fieldDefinition);
    }

    public function mapToFieldValueType(FieldDefinition $fieldDefinition): string
    {
        return $this->innerMapper->mapToFieldValueType($fieldDefinition);
    }

    public function mapToFieldValueResolver(FieldDefinition $fieldDefinition): string
    {
        $resolver = $this->innerMapper->mapToFieldValueResolver($fieldDefinition) ?? '';

        $replacements = [
            'content' => 'value.getContent()',
            'location' => 'value.getLocation()',
            'item' => 'value',
            'field' => 'query("ItemFieldValue", value, "' . $fieldDefinition->identifier . '", args)',
        ];

        // Only bare variables are replaced: quoted strings (like the field's identifier) and members (field.location) are skipped.
        // Possessive quantifiers keep long string literals from exhausting the PCRE JIT stack.
        $resolver = preg_replace_callback(
            '/(?:"(?:[^"\\\\]++|\\\\.)*+"|\'(?:[^\'\\\\]++|\\\\.)*+\')(*SKIP)(*FAIL)|(?<![.\w$])(content|location|item|field)\b/',
            static fn (array $matches): string => $replacements[$matches[1]],
            $resolver
        );

        if ($resolver === null) {
            throw new UnexpectedValueException(sprintf(
                'Failed to replace the resolver variables of field definition "%s" (PCRE error %d)',
                $fieldDefinition->identifier,
                preg_last_error()
            ));
        }

        return $resolver;
    }

    public function mapToFieldValueInputType(ContentType $contentType, FieldDefinition $fieldDefinition): ?string
    {
        return $this->innerMapper->mapToFieldValueInputType($contentType, $fieldDefinition);
    }

    public function mapToFieldValueArgsBuilder(FieldDefinition $fieldDefinition): ?string
    {
        return $this->innerMapper->mapToFieldValueArgsBuilder($fieldDefinition);
    }
}

class_alias(ResolverVariables::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Mapper\FieldDefinition\ResolverVariables');
