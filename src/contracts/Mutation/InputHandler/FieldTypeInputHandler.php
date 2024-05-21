<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Contracts\GraphQL\Mutation\InputHandler;

use Ibexa\Contracts\Core\FieldType\Value;

interface FieldTypeInputHandler
{
    public function toFieldValue($input, $inputFormat = null): Value;
}
