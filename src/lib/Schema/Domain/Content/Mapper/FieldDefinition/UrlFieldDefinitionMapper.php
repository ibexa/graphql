<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\GraphQL\Schema\Domain\Content\Mapper\FieldDefinition\FieldDefinitionMapper;

final class UrlFieldDefinitionMapper extends DecoratingFieldDefinitionMapper implements FieldDefinitionMapper
{
    private const FIELD_TYPE_IDENTIFIER = 'ezurl';

    private bool $shouldExtendUrlInputType;

    public function __construct(
        FieldDefinitionMapper $innerMapper,
        bool $shouldExtendUrlInputType
    ) {
        parent::__construct($innerMapper);
        $this->shouldExtendUrlInputType = $shouldExtendUrlInputType;
    }

    public function mapToFieldValueType(FieldDefinition $fieldDefinition): string
    {
        $type = parent::mapToFieldValueType($fieldDefinition);
        if (!$this->canMap($fieldDefinition)) {
            return $type;
        }

        if ($this->shouldExtendUrlInputType) {
            $type = 'UrlFieldValue';
        } else {
            @trigger_error(
                'The return type `string` for the URL field has been deprecated since version 4.6 ' .
                'and will be removed in version 5.0. To start receiving `UrlFieldInput` instead of the deprecated ' .
                '`string`, set the parameter `ibexa.graphql.schema.should.extend.ezurl` to `true`.',
                E_USER_DEPRECATED
            );
        }

        return $type;
    }

    protected function getFieldTypeIdentifier(): string
    {
        return self::FIELD_TYPE_IDENTIFIER;
    }
}
