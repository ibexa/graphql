<?php

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\AddDomainGroupToDomain;
use spec\Ibexa\GraphQL\Tools\FieldArgument;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Prophecy\Argument;

class AddDomainGroupToDomainSpec extends ContentTypeGroupWorkerBehavior
{
    const DOMAIN_TYPE = 'Domain';
    const GROUP_TYPE = 'DomainGroupTestGroup';
    const GROUP_FIELD = 'testGroup';

    public function let(NameHelper $nameHelper, ContentTypeService $contentTypeService)
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

    function it_is_initializable()
    {
        $this->shouldHaveType(AddDomainGroupToDomain::class);
    }

    function it_can_not_work_if_args_do_not_have_ContentTypeGroup(
        SchemaBuilder $schema
    )
    {
        $this->canWork($schema, [])->shouldBe(false);
    }

    function it_can_not_work_if_the_field_is_already_defined(
        SchemaBuilder $schema
    )
    {
        $schema->hasTypeWithField(self::DOMAIN_TYPE, self::GROUP_FIELD)->willReturn(true);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    function it_can_not_work_if_the_group_is_empty(
        SchemaBuilder $schema,
        ContentTypeService $contentTypeService
    )
    {
        $schema->hasTypeWithField(self::DOMAIN_TYPE, self::GROUP_FIELD)->willReturn(false);
        $contentTypeService->loadContentTypes(Argument::type(ContentTypeGroup::class))->willReturn([]);
        $this->canWork($schema, $this->args())->shouldBe(false);
    }

    function it_adds_a_field_for_the_group_to_the_Domain_object(
        SchemaBuilder $schema
    )
    {
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

class_alias(AddDomainGroupToDomainSpec::class, 'spec\EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentTypeGroup\AddDomainGroupToDomainSpec');
