<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType\AddItemOfTypeConnectionToGroup;
use Prophecy\Argument;
use spec\Ibexa\GraphQL\Tools\ContentTypeArgument;
use spec\Ibexa\GraphQL\Tools\ContentTypeGroupArgument;
use spec\Ibexa\GraphQL\Tools\FieldArgArgument;
use spec\Ibexa\GraphQL\Tools\FieldArgument;

class AddItemOfTypeConnectionToGroupSpec extends ContentTypeWorkerBehavior
{
    public const GROUP_TYPE = 'ItemTestGroup';
    public const TYPE_TYPE = 'TestItemConnection';
    public const CONNECTION_FIELD = 'testTypes';

    public function let(NameHelper $nameHelper): void
    {
        $this->setNameHelper($nameHelper);

        $nameHelper
            ->itemGroupName(ContentTypeGroupArgument::withIdentifier(self::GROUP_IDENTIFIER))
            ->willReturn(self::GROUP_TYPE);

        $nameHelper
            ->itemConnectionField(ContentTypeArgument::withIdentifier(self::TYPE_IDENTIFIER))
            ->willReturn(self::CONNECTION_FIELD);

        $nameHelper
            ->itemConnectionName(ContentTypeArgument::withIdentifier(self::TYPE_IDENTIFIER))
            ->willReturn(self::TYPE_TYPE);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddItemOfTypeConnectionToGroup::class);
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

    public function it_can_not_work_if_the_collection_field_is_already_set(SchemaBuilder $schema): void
    {
        $schema->hasTypeWithField(self::GROUP_TYPE, self::CONNECTION_FIELD)->willReturn(true);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    public function it_adds_a_collection_field_for_the_ContentType_to_the_ContentTypeGroup(SchemaBuilder $schema): void
    {
        $schema
            ->addFieldToType(
                self::GROUP_TYPE,
                Argument::allOf(
                    FieldArgument::hasName(self::CONNECTION_FIELD),
                    FieldArgument::hasType(self::TYPE_TYPE),
                    FieldArgument::hasDescription(self::TYPE_DESCRIPTION),
                    FieldArgument::withResolver('ItemsOfTypeAsConnection')
                )
            )
            ->shouldBeCalled();

        $schema
            ->addArgToField(
                self::GROUP_TYPE,
                self::CONNECTION_FIELD,
                Argument::allOf(
                    FieldArgArgument::withName('query'),
                    FieldArgArgument::withType('ContentSearchQuery')
                )
            )
            ->shouldBeCalled();

        $schema
            ->addArgToField(
                self::GROUP_TYPE,
                self::CONNECTION_FIELD,
                Argument::allOf(
                    FieldArgArgument::withName('sortBy'),
                    FieldArgArgument::withType('[SortByOptions]')
                )
            )
            ->shouldBeCalled();

        $this->work($schema, $this->args());
    }
}
