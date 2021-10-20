<?php
namespace Ibexa\Spec\GraphQL\Tools;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Prophecy\Argument\Token\CallbackToken;

class ContentTypeArgument
{
    /**
     * @return ContentType|CallbackToken
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
