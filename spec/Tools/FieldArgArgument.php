<?php
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

    private static function has($property, $value) {
        return new CallbackToken(
            function(Input\Arg $arg) use ($property, $value) {
                return $arg->$property === $value;
            }
        );
    }
}
class_alias(FieldArgArgument::class, 'spec\EzSystems\EzPlatformGraphQL\Tools\FieldArgArgument');
