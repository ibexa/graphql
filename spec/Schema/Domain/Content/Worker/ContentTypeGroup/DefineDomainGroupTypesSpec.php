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
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\DefineDomainGroupTypes;
use Prophecy\Argument;
use spec\Ibexa\GraphQL\Tools\TypeArgument;

class DefineDomainGroupTypesSpec extends ContentTypeGroupWorkerBehavior
{
    public const GROUP_TYPES_TYPE = 'DomainGroupTestTypes';

    public function let(NameHelper $nameHelper, ContentTypeService $contentTypeService): void
    {
        $this->beConstructedWith($contentTypeService);
        $this->setNameHelper($nameHelper);

        $nameHelper
            ->itemGroupTypesName(Argument::type(ContentTypeGroup::class))
            ->willReturn(self::GROUP_TYPES_TYPE);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DefineDomainGroupTypes::class);
    }

    public function it_can_not_work_if_args_do_not_have_ContentTypeGroup(
        SchemaBuilder $schema
    ): void {
        $this->canWork($schema, [])->shouldBe(false);
    }

    public function it_can_not_work_if_the_type_is_already_defined(
        SchemaBuilder $schema
    ): void {
        $schema->hasType(self::GROUP_TYPES_TYPE)->willReturn(true);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    public function it_can_not_work_if_the_group_is_empty(
        SchemaBuilder $schema,
        ContentTypeService $contentTypeService
    ): void {
        $schema->hasType(self::GROUP_TYPES_TYPE)->willReturn(false);
        $contentTypeService->loadContentTypes(Argument::type(ContentTypeGroup::class))->willReturn([]);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    public function it_defines_the_DomainGroupTypes_object(
        SchemaBuilder $schema
    ): void {
        $schema
            ->addType(
                Argument::allOf(
                    TypeArgument::isNamed(self::GROUP_TYPES_TYPE),
                    TypeArgument::hasType('object')
                )
            )
            ->shouldBeCalled();
        $this->work($schema, $this->args());
    }
}
