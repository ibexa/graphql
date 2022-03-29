<?php

namespace spec\Ibexa\GraphQL\Schema\Domain\Content;

use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Schema\Domain\Pluralizer;
use PhpSpec\ObjectBehavior;

class NameHelperSpec extends ObjectBehavior
{
    public function let(Pluralizer $pluralizer): void
    {
        $this->beConstructedWith(
            ['id'=>'id_'],
            $pluralizer
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(NameHelper::class);
    }

    public function it_removes_special_characters_from_ContentTypeGroup_identifier(): void
    {
        $contentTypeGroup = new ContentTypeGroup(['identifier' => 'Name with-hyphen']);
        $this->itemGroupName($contentTypeGroup)->shouldBe('ItemGroupNameWithHyphen');
    }

    public function it_removes_field_type_identifier_colisions(): void
    {
        $fieldDefinition = new FieldDefinition(['identifier' => 'id']);
        $this->fieldDefinitionField($fieldDefinition)->shouldBe('id_');
    }
}

class_alias(NameHelperSpec::class, 'spec\EzSystems\EzPlatformGraphQL\Schema\Domain\Content\NameHelperSpec');
