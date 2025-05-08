<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldTypeInputHandler;
use Ibexa\Core\FieldType\FieldType;

/**
 * Converts input to a Field Value using the type's fromHash method.
 */
class FromHash implements FieldTypeInputHandler
{
    private FieldType $fieldType;

    public function __construct(FieldType $fieldType)
    {
        $this->fieldType = $fieldType;
    }

    public function toFieldValue($input, $inputFormat = null): Value
    {
        return $this->fieldType->fromHash($input);
    }
}
