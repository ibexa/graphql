<?php

namespace Ibexa\Spec\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\DefineDomainGroupTypes;
use Ibexa\Spec\GraphQL\Tools\TypeArgument;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Prophecy\Argument;

class DefineDomainGroupTypesSpec extends ContentTypeGroupWorkerBehavior
{
    const GROUP_TYPES_TYPE = 'DomainGroupTestTypes';

    public function let(NameHelper $nameHelper, ContentTypeService $contentTypeService)
    {
        $this->beConstructedWith($contentTypeService);
        $this->setNameHelper($nameHelper);

        $nameHelper
            ->domainGroupTypesName(Argument::type(ContentTypeGroup::class))
            ->willReturn(self::GROUP_TYPES_TYPE);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DefineDomainGroupTypes::class);
    }

    function it_can_not_work_if_args_do_not_have_ContentTypeGroup(
        SchemaBuilder $schema
    )
    {
        $this->canWork($schema, [])->shouldBe(false);
    }

    function it_can_not_work_if_the_type_is_already_defined(
        SchemaBuilder $schema
    )
    {
        $schema->hasType(self::GROUP_TYPES_TYPE)->willReturn(true);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    function it_can_not_work_if_the_group_is_empty(
        SchemaBuilder $schema,
        ContentTypeService $contentTypeService
    )
    {
        $schema->hasType(self::GROUP_TYPES_TYPE)->willReturn(false);
        $contentTypeService->loadContentTypes(Argument::type(ContentTypeGroup::class))->willReturn([]);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    function it_defines_the_DomainGroupTypes_object(
        SchemaBuilder $schema
    )
    {
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

class_alias(DefineDomainGroupTypesSpec::class, 'spec\EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\DefineDomainGroupTypesSpec');
