<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\AddDomainGroupToDomain;
use Prophecy\Argument;
use spec\Ibexa\GraphQL\Tools\FieldArgument;

class AddDomainGroupToDomainSpec extends ContentTypeGroupWorkerBehavior
{
    public const DOMAIN_TYPE = 'Domain';
    public const GROUP_TYPE = 'DomainGroupTestGroup';
    public const GROUP_FIELD = 'testGroup';

    public function let(NameHelper $nameHelper, ContentTypeService $contentTypeService): void
    {
        $this->beConstructedWith($contentTypeService);
        $this->setNameHelper($nameHelper);

        $nameHelper
            ->itemGroupName(Argument::type(ContentTypeGroup::class))
            ->willReturn(self::GROUP_TYPE);

        $nameHelper
            ->itemGroupField(Argument::type(ContentTypeGroup::class))
            ->willReturn(self::GROUP_FIELD);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddDomainGroupToDomain::class);
    }

    public function it_can_not_work_if_args_do_not_have_ContentTypeGroup(
        SchemaBuilder $schema
    ): void {
        $this->canWork($schema, [])->shouldBe(false);
    }

    public function it_can_not_work_if_the_field_is_already_defined(
        SchemaBuilder $schema
    ): void {
        $schema->hasTypeWithField(self::DOMAIN_TYPE, self::GROUP_FIELD)->willReturn(true);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    public function it_can_not_work_if_the_group_is_empty(
        SchemaBuilder $schema,
        ContentTypeService $contentTypeService
    ): void {
        $schema->hasTypeWithField(self::DOMAIN_TYPE, self::GROUP_FIELD)->willReturn(false);
        $contentTypeService->loadContentTypes(Argument::type(ContentTypeGroup::class))->willReturn([]);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    public function it_adds_a_field_for_the_group_to_the_Domain_object(
        SchemaBuilder $schema
    ): void {
        $schema
            ->addFieldToType(
                self::DOMAIN_TYPE,
                Argument::allOf(
                    FieldArgument::hasName(self::GROUP_FIELD),
                    FieldArgument::hasType(self::GROUP_TYPE),
                    FieldArgument::hasDescription(self::GROUP_DESCRIPTION)
                )
            )
            ->shouldBeCalled();
        $this->work($schema, $this->args());
    }
}
