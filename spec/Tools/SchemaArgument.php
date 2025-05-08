<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Tools;

use Prophecy\Argument;
use Prophecy\Argument\Token\CallbackToken;

class SchemaArgument extends Argument
{
    public static function isSchema(): CallbackToken
    {
        return new CallbackToken(
            static function ($schema): bool {
                return is_array($schema);
            }
        );
    }
}
