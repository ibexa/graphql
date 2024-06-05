<?php
namespace spec\Ibexa\GraphQL\Tools;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Prophecy\Argument\Token\CallbackToken;

class ContentTypeGroupArgument
{
    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup|\Prophecy\Argument\Token\CallbackToken
     */
    public static function withIdentifier($identifier)
    {
        return new CallbackToken(
            function ($argument) use ($identifier) {
                return
                    $argument instanceof ContentTypeGroup
                    && $argument->identifier === $identifier;
            }
        );
    }
}
