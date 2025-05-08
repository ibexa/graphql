<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content\Worker;

use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;

class BaseWorker
{
    private ?NameHelper $nameHelper = null;

    public function setNameHelper(NameHelper $nameHelper): void
    {
        $this->nameHelper = $nameHelper;
    }

    protected function getNameHelper()
    {
        return $this->nameHelper;
    }
}
