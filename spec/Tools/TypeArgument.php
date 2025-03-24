<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Tools;

use Ibexa\GraphQL\Schema\Builder\Input;
use Prophecy\Argument\Token\CallbackToken;

class TypeArgument
{
    public static function isNamed($name): CallbackToken
    {
        return new CallbackToken(
            static function (Input\Type $type) use ($name): bool {
                return $type->name === $name;
            }
        );
    }

    public static function inherits($typeName): CallbackToken
    {
        return new CallbackToken(
            static function (Input\type $typeInput) use ($typeName): bool {
                return
                    is_array($typeInput->inherits)
                    ? in_array($typeName, $typeInput->inherits)
                    : $typeInput->inherits === $typeName;
            }
        );
    }

    public static function implements($interfaceName): CallbackToken
    {
        return new CallbackToken(
            static function (Input\type $typeInput) use ($interfaceName): bool {
                return
                    is_array($typeInput->interfaces)
                        ? in_array($interfaceName, $typeInput->interfaces)
                        : $typeInput->interfaces === $interfaceName;
            }
        );
    }

    public static function hasType($expectedType): CallbackToken
    {
        return new CallbackToken(
            static function (Input\Type $type) use ($expectedType): bool {
                return $type->type === $expectedType;
            }
        );
    }
}
