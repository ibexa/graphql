<?php

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\DefineDomainGroup;
use spec\Ibexa\GraphQL\Tools\FieldArgument;
use spec\Ibexa\GraphQL\Tools\TypeArgument;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Prophecy\Argument;

class DefineDomainGroupSpec extends ContentTypeGroupWorkerBehavior
{
    const GROUP_TYPE = 'DomainGroupTest';
    const GROUP_TYPES_TYPE = 'DomainGroupTestTypes';

    public function let(NameHelper $nameHelper, ContentTypeService $contentTypeService)
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

    function it_is_initializable()
    {
        $this->shouldHaveType(DefineDomainGroup::class);
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
        $schema->hasType(self::GROUP_TYPE)->willReturn(true);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    function it_can_not_work_if_the_group_is_empty(
        SchemaBuilder $schema,
        ContentTypeService $contentTypeService
    )
    {
        $schema->hasType(self::GROUP_TYPE)->willReturn(true);
        $contentTypeService->loadContentTypes(Argument::type(ContentTypeGroup::class))->willReturn([]);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    function it_defines_the_DomainGroup_object(
        SchemaBuilder $schema
    )
    {
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

class_alias(DefineDomainGroupSpec::class, 'spec\EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\DefineDomainGroupSpec');
