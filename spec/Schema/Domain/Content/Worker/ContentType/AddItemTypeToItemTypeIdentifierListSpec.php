<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType\AddItemTypeToItemTypeIdentifierList;
use Prophecy\Argument;
use spec\Ibexa\GraphQL\Tools\EnumValueArgument;

class AddItemTypeToItemTypeIdentifierListSpec extends ContentTypeWorkerBehavior
{
    public const ENUM_TYPE = 'ContentTypeIdentifier';

    public function let(NameHelper $nameHelper): void
    {
        $this->setNameHelper($nameHelper);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddItemTypeToItemTypeIdentifierList::class);
    }

    public function it_can_not_work_if_args_do_not_have_a_ContentType(SchemaBuilder $schema): void
    {
        $args = $this->args();
        unset($args['ContentType']);
        $this->canWork($schema, [])->shouldBe(false);
    }

    public function it_can_not_work_if_the_ContentTypeIdentifier_enum_is_not_defined(SchemaBuilder $schema): void
    {
        $schema->hasType(self::ENUM_TYPE)->willReturn(false);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    public function it_adds_a_field_for_the_ContentType_to_the_ContentTypeIdentifier_enum(SchemaBuilder $schema): void
    {
        $schema
            ->addValueToEnum(
                self::ENUM_TYPE,
                Argument::allOf(
                    EnumValueArgument::withName(self::TYPE_IDENTIFIER),
                    EnumValueArgument::withDescription(self::TYPE_DESCRIPTION)
                )
            )
            ->shouldBeCalled();
        $this->work($schema, $this->args());
    }
}
