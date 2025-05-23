<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType;

use Ibexa\GraphQL\Schema\Domain\Content\Worker\ContentType\AddItemToGroup;

class AddItemToGroupSpec extends ContentTypeWorkerBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddItemToGroup::class);
    }
}
