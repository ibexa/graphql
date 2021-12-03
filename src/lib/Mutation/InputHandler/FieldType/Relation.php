<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldTypeInputHandler;

/**
 * Converts input to a Field Value using the type's fromHash method.
 */
class Relation extends FromHash implements FieldTypeInputHandler
{
    public function toFieldValue($input, $inputFormat = null): Value
    {
        return parent::toFieldValue(
            ['destinationContentId' => $input],
            null
        );
    }
}

class_alias(Relation::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Mutation\InputHandler\FieldType\Relation');
