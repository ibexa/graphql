<?php

namespace Ibexa\Spec\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType\AddItemToGroup;

class AddItemToGroupSpec extends ContentTypeWorkerBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddItemToGroup::class);
    }
}

class_alias(AddItemToGroupSpec::class, 'spec\EzSystems\EzPlatformGraphQL\Schema\Domain\Content\Worker\ContentType\AddItemToGroupSpec');
