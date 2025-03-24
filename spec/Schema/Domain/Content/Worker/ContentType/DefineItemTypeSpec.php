<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType\DefineItemType;
use Prophecy\Argument;
use spec\Ibexa\GraphQL\Tools\ContentTypeArgument;
use spec\Ibexa\GraphQL\Tools\TypeArgument;

class DefineItemTypeSpec extends ContentTypeWorkerBehavior
{
    public const TYPE_TYPE = 'TestTypeType';

    public function let(NameHelper $nameHelper): void
    {
        $nameHelper
            ->itemTypeName(ContentTypeArgument::withIdentifier(self::TYPE_IDENTIFIER))
            ->willReturn(self::TYPE_TYPE);

        $this->setNameHelper($nameHelper);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DefineItemType::class);
    }

    public function it_can_not_work_if_args_do_not_include_a_ContentTypeGroup(SchemaBuilder $schema): void
    {
        $this->canWork($schema, [])->shouldBe(false);
    }

    public function it_can_not_work_if_args_do_not_include_a_ContentType(SchemaBuilder $schema): void
    {
        $args = $this->args();
        unset($args['ContentType']);
        $this->canWork($schema, $args)->shouldBe(false);
    }

    public function it_defines_a_DomainContent_type_based_on_the_ContentType(SchemaBuilder $schema): void
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
