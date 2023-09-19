<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * A FieldValue Proxy that holds the content and field definition identifier.
 *
 * Required to be able to identify a value's FieldType to map with a GraphQL type.
 *
 * @property int $contentTypeId
 * @property string $fieldDefIdentifier
 * @property object $value
 */
class ContentFieldValue extends ValueObject
{
    /**
     * Identifier of the field definition this value is from.
     */
    protected $fieldDefIdentifier;

    /**
     * Id of the Content Type this value is from.
     */
    protected $contentTypeId;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    protected $content;

    /**
     * @var \Ibexa\Core\FieldType\Value
     */
    protected $value;

    public function __get($property)
    {
        if (property_exists($this->value, $property)) {
            return $this->value->$property;
        }

        return parent::__get($property);
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}

class_alias(ContentFieldValue::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Value\ContentFieldValue');
