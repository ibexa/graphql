<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Tools;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Prophecy\Argument\Token\CallbackToken;

class ContentTypeGroupArgument
{
    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup|\Prophecy\Argument\Token\CallbackToken
     */
    public static function withIdentifier($identifier): CallbackToken
    {
        return new CallbackToken(
            static function ($argument) use ($identifier): bool {
                return
                    $argument instanceof ContentTypeGroup
                    && $argument->identifier === $identifier;
            }
        );
    }
}
