<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldTypeInputHandler;
use Ibexa\GraphQL\Exception\UnsupportedFieldInputFormatException;

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
