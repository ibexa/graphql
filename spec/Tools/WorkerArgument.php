<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Tools;

use PhpSpec\Wrapper\ObjectWrapper;
use Prophecy\Argument;
use Prophecy\Argument\Token\CallbackToken;

class WorkerArgument extends Argument
{
    public static function hasContentTypeGroup($contentTypeGroup = null)
    {
        return self::hasArgument('ContentTypeGroup', $contentTypeGroup);
    }

    public static function hasContentType($contentType = null)
    {
        return self::hasArgument('ContentType', $contentType);
    }

    public static function hasFieldDefinition($fieldDefinition = null)
    {
        return self::hasArgument('FieldDefinition', $fieldDefinition);
    }

    public static function hasArgument($argumentName, $value = null): CallbackToken
    {
        return new CallbackToken(
            static function ($args) use ($argumentName, $value): bool {
                if ($value instanceof ObjectWrapper) {
                    $value = $value->getWrappedObject();
                }

                return isset($args[$argumentName]) && ($value === null || $args[$argumentName] == $value);
            }
        );
    }
}
