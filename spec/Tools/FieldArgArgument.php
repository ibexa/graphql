<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Tools;

use Ibexa\GraphQL\Schema\Builder\Input;
use Prophecy\Argument\Token\CallbackToken;

class FieldArgArgument
{
    public static function withName($name)
    {
        return self::has('name', $name);
    }

    public static function withDescription($description)
    {
        return self::has('description', $description);
    }

    public static function withType($type)
    {
        return self::has('type', $type);
    }

    private static function has(string $property, $value): CallbackToken
    {
        return new CallbackToken(
            static function (Input\Arg $arg) use ($property, $value): bool {
                return $arg->$property === $value;
            }
        );
    }
}
