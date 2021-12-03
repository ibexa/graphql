<?php

namespace Ibexa\Spec\GraphQL\Schema\Domain\Content;

use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NameHelperSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['id'=>'id_']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NameHelper::class);
    }

    function it_removes_special_characters_from_ContentTypeGroup_identifier()
    {
        $contentTypeGroup = new ContentTypeGroup(['identifier' => 'Name with-hyphen']);
        $this->itemGroupName($contentTypeGroup)->shouldBe('ItemGroupNameWithHyphen');
    }

    function it_removes_field_type_identifier_colisions()
    {
        $fieldDefinition = new FieldDefinition(['identifier' => 'id']);
        $this->fieldDefinitionField($fieldDefinition)->shouldBe('id_');
    }
}

class_alias(NameHelperSpec::class, 'spec\EzSystems\EzPlatformGraphQL\Schema\Domain\Content\NameHelperSpec');
