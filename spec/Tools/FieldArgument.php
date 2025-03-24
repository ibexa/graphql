<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Tools;

use Ibexa\GraphQL\Schema\Builder\Input;
use Prophecy\Argument\Token\CallbackToken;

class FieldArgument
{
    public static function hasName($name)
    {
        return self::has('name', $name);
    }

    public static function hasType($type)
    {
        return self::has('type', $type);
    }

    public static function hasDescription($description)
    {
        return self::has('description', $description);
    }

    public static function withResolver($resolverFunction): CallbackToken
    {
        return new CallbackToken(
            static function (Input\Field $input) use ($resolverFunction): bool {
                return strpos($input->resolve, $resolverFunction) !== false;
            }
        );
    }

    private static function has(string $property, $value): CallbackToken
    {
        return new CallbackToken(
            static function (Input\Field $field) use ($property, $value): bool {
                return $field->$property === $value;
            }
        );
    }
}
