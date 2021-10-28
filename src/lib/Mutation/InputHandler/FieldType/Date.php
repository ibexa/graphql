<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType;

use eZ\Publish\SPI\FieldType\Value;
use Ibexa\GraphQL\Exception\UnsupportedFieldInputFormatException;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldTypeInputHandler;

/**
 * Converts input to a Field Value using the type's fromHash method.
 */
class Date extends FromHash implements FieldTypeInputHandler
{
    public function toFieldValue($input, $inputFormat = null): Value
    {
        if ($inputFormat === null) {
            $inputFormat = 'timestring';
        }

        if (!in_array($inputFormat, ['timestring', 'rfc850', 'timestamp'])) {
            throw new UnsupportedFieldInputFormatException('ezdate', $inputFormat);
        }

        return parent::toFieldValue(
            [$inputFormat => $input],
            null
        );
    }
}

class_alias(Date::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Mutation\InputHandler\FieldType\Date');