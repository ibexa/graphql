<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use DateTime;

/**
 * @internal
 */
class DateResolver
{
    public function resolveDateToFormat($date, $args)
    {
        if (!$date instanceof DateTime) {
            return null;
        }

        if (isset($args['pattern'])) {
            return $date->format($args['pattern']);
        }

        if (isset($args['constant'])) {
            return $date->format($args['constant']);
        }
    }
}
