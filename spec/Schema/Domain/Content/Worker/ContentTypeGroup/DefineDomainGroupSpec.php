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
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\DefineDomainGroup;
use Prophecy\Argument;
use spec\Ibexa\GraphQL\Tools\FieldArgument;
use spec\Ibexa\GraphQL\Tools\TypeArgument;

class DefineDomainGroupSpec extends ContentTypeGroupWorkerBehavior
{
    public const GROUP_TYPE = 'DomainGroupTest';
    public const GROUP_TYPES_TYPE = 'DomainGroupTestTypes';

    public function let(NameHelper $nameHelper, ContentTypeService $contentTypeService): void
    {
        $this->beConstructedWith($contentTypeService);
        $this->setNameHelper($nameHelper);

        $nameHelper
            ->itemGroupName(Argument::type(ContentTypeGroup::class))
            ->willReturn(self::GROUP_TYPE);

        $nameHelper
            ->itemGroupTypesName(Argument::type(ContentTypeGroup::class))
            ->willReturn(self::GROUP_TYPES_TYPE);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DefineDomainGroup::class);
    }

    public function it_can_not_work_if_args_do_not_have_ContentTypeGroup(
        SchemaBuilder $schema
    ): void {
        $this->canWork($schema, [])->shouldBe(false);
    }

    public function it_can_not_work_if_the_type_is_already_defined(
        SchemaBuilder $schema
    ): void {
        $schema->hasType(self::GROUP_TYPE)->willReturn(true);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    public function it_can_not_work_if_the_group_is_empty(
        SchemaBuilder $schema,
        ContentTypeService $contentTypeService
    ): void {
        $schema->hasType(self::GROUP_TYPE)->willReturn(true);
        $contentTypeService->loadContentTypes(Argument::type(ContentTypeGroup::class))->willReturn([]);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    public function it_defines_the_DomainGroup_object(
        SchemaBuilder $schema
    ): void {
        $schema
            ->addType(
                Argument::allOf(
                    TypeArgument::isNamed(self::GROUP_TYPE),
                    TypeArgument::hasType('object'),
                    TypeArgument::inherits('DomainContentTypeGroup')
                )
            )
            ->shouldBeCalled();

        $schema
            ->addFieldToType(
                self::GROUP_TYPE,
                Argument::allOf(
                    FieldArgument::hasName('_types'),
                    FieldArgument::hasType(self::GROUP_TYPES_TYPE)
                )
            )
            ->shouldBeCalled();

        $this->work($schema, $this->args());
    }
}
