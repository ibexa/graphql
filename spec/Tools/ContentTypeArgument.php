<?php
namespace Ibexa\Spec\GraphQL\Tools;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Prophecy\Argument\Token\CallbackToken;

class ContentTypeArgument
{
    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType|\Prophecy\Argument\Token\CallbackToken
     */
    public static function withIdentifier($identifier)
    {
        return new CallbackToken(
            function ($argument) use ($identifier) {
                return
                    $argument instanceof ContentType
                    && $argument->identifier === $identifier;
            }
        );
    }
}
class_alias(ContentTypeArgument::class, 'spec\EzSystems\EzPlatformGraphQL\Tools\ContentTypeArgument');
