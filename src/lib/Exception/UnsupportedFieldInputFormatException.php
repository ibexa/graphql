<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Exception;

use InvalidArgumentException;

class UnsupportedFieldInputFormatException extends InvalidArgumentException
{
    public function __construct($fieldType, $format)
    {
        parent::__construct("Unsupported $fieldType input format $format");
    }
}

class_alias(UnsupportedFieldInputFormatException::class, 'EzSystems\EzPlatformGraphQL\Exception\UnsupportedFieldInputFormatException');
