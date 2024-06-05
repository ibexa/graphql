<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Exception;

use Exception;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

class NoValidSiteaccessException extends Exception
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    private $location;

    public function __construct(Location $location)
    {
        parent::__construct("Could not find a suitable siteaccess for the location with id $location->id");
        $this->location = $location;
    }
}
