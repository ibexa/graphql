<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\InputMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;

interface QueryMapper
{
    public function mapInputToLocationQuery(array $inputArray): LocationQuery;

    public function mapInputToQuery(array $inputArray): Query;
}
