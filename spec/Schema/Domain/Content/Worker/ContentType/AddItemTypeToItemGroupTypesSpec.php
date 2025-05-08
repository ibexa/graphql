<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType\AddItemTypeToItemGroupTypes;
use Prophecy\Argument;
use spec\Ibexa\GraphQL\Tools\ContentTypeArgument;
use spec\Ibexa\GraphQL\Tools\ContentTypeGroupArgument;
use spec\Ibexa\GraphQL\Tools\FieldArgument;

class AddItemTypeToItemGroupTypesSpec extends ContentTypeWorkerBehavior
{
    public const GROUP_TYPES_TYPE = 'DomainGroupTestGroupTypes';

    public const TYPE_FIELD = 'testType';
    public const TYPE_TYPE = 'TestTypeType';

    public function let(NameHelper $nameHelper): void
    {
        $this->setNameHelper($nameHelper);

        $nameHelper
            ->itemField(
                ContentTypeArgument::withIdentifier(self::TYPE_IDENTIFIER)
            )
            ->willReturn(self::TYPE_FIELD);

        $nameHelper
            ->itemTypeName(
                ContentTypeArgument::withIdentifier(self::TYPE_IDENTIFIER)
            )
            ->willReturn(self::TYPE_TYPE);

        $nameHelper
            ->itemGroupTypesName(
                ContentTypeGroupArgument::withIdentifier(self::GROUP_IDENTIFIER)
            )
            ->willReturn(self::GROUP_TYPES_TYPE);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddItemTypeToItemGroupTypes::class);
    }

    public function it_can_not_work_if_args_do_not_have_a_ContentTypeGroup(SchemaBuilder $schema): void
    {
        $this->canWork($schema, [])->shouldBe(false);
    }

    public function it_can_not_work_if_args_do_not_have_a_ContentType(SchemaBuilder $schema): void
    {
        $args = $this->args();
        unset($args['ContentType']);
        $this->canWork($schema, $args)->shouldBe(false);
    }

    public function it_can_not_work_if_the_field_is_already_defined(SchemaBuilder $schema): void
    {
        $schema->hasTypeWithField(self::GROUP_TYPES_TYPE, self::TYPE_FIELD)->willReturn(true);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    public function it_adds_a_field_for_the_ContentType_to_the_DomainGroupTypes_object(SchemaBuilder $schema): void
    {
        $schema
            ->addFieldToType(
                self::GROUP_TYPES_TYPE,
                Argument::allOf(
                    FieldArgument::hasName(self::TYPE_FIELD),
                    FieldArgument::hasType(self::TYPE_TYPE)
                )
            )
            ->shouldBeCalled();
        $this->work($schema, $this->args());
    }
}
