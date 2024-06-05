<?php

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType\AddItemToGroup;

class AddItemToGroupSpec extends ContentTypeWorkerBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddItemToGroup::class);
    }
}
