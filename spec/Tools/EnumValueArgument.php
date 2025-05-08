<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Tools;

use Ibexa\GraphQL\Schema\Builder\Input;
use Prophecy\Argument\Token\CallbackToken;

class EnumValueArgument
{
    public static function withName($name): CallbackToken
    {
        return new CallbackToken(
            static function (Input\EnumValue $input) use ($name): bool {
                return $input->name === $name;
            }
        );
    }

    public static function withDescription($description): CallbackToken
    {
        return new CallbackToken(
            static function (Input\EnumValue $input) use ($description): bool {
                return $input->description === $description;
            }
        );
    }

    public static function withValue($value): CallbackToken
    {
        return new CallbackToken(
            static function (Input\EnumValue $input) use ($value): bool {
                return $input->value === $value;
            }
        );
    }
}
