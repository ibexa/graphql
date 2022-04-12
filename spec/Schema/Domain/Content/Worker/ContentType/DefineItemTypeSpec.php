<?php

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType\DefineItemType;
use spec\Ibexa\GraphQL\Tools\ContentTypeArgument;
use spec\Ibexa\GraphQL\Tools\TypeArgument;
use Prophecy\Argument;

class DefineItemTypeSpec extends ContentTypeWorkerBehavior
{
    const TYPE_TYPE = 'TestTypeType';

    function let(NameHelper $nameHelper)
    {
        $nameHelper
            ->itemTypeName(ContentTypeArgument::withIdentifier(self::TYPE_IDENTIFIER))
            ->willReturn(self::TYPE_TYPE);

        $this->setNameHelper($nameHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DefineItemType::class);
    }

    function it_can_not_work_if_args_do_not_include_a_ContentTypeGroup(SchemaBuilder $schema)
    {
        $this->canWork($schema, [])->shouldBe(false);
    }

    function it_can_not_work_if_args_do_not_include_a_ContentType(SchemaBuilder $schema)
    {
        $args = $this->args();
        unset($args['ContentType']);
        $this->canWork($schema, $args)->shouldBe(false);
    }

    function it_defines_a_DomainContent_type_based_on_the_ContentType(SchemaBuilder $schema)
    {
        $schema
            ->addType(Argument::allOf(
                TypeArgument::isNamed(self::TYPE_TYPE),
                TypeArgument::hasType('object'),
                TypeArgument::inherits('BaseItemType'),
                TypeArgument::implements('ItemType')
            ))
            ->shouldBeCalled();

        $this->work($schema, $this->args());
    }
}

class_alias(DefineItemTypeSpec::class, 'spec\EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentType\DefineItemTypeSpec');
