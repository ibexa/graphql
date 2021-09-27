<?php
namespace Ibexa\Spec\GraphQL\Tools;

use PhpSpec\Wrapper\ObjectWrapper;
use Prophecy\Argument;

class SchemaArgument extends Argument
{
    public static function isSchema()
    {
        return new Argument\Token\CallbackToken(
            function ($schema) {
                return is_array($schema);
            }
        );
    }
}
class_alias(SchemaArgument::class, 'spec\EzSystems\EzPlatformGraphQL\Tools\SchemaArgument');
