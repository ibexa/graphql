<?php
namespace Ibexa\Spec\GraphQL\Tools;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use Prophecy\Argument\Token\CallbackToken;

class ContentTypeGroupArgument
{
    /**
     * @return ContentTypeGroup|CallbackToken
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
class_alias(ContentTypeGroupArgument::class, 'spec\EzSystems\EzPlatformGraphQL\Tools\ContentTypeGroupArgument');
