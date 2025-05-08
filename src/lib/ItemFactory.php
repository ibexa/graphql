<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\Resolver\LocationGuesser\LocationGuesser;
use Ibexa\GraphQL\Resolver\SiteaccessGuesser\SiteaccessGuesser;
use Ibexa\GraphQL\Value\Item;

class ItemFactory
{
    private LocationGuesser $locationGuesser;

    private SiteaccessGuesser $siteaccessGuesser;

    public function __construct(
        LocationGuesser $locationGuesser,
        SiteaccessGuesser $siteaccessGuesser
    ) {
        $this->locationGuesser = $locationGuesser;
        $this->siteaccessGuesser = $siteaccessGuesser;
    }

    public function fromContent(Content $content): Item
    {
        return Item::fromContent(
            $this->locationGuesser,
            $this->siteaccessGuesser,
            $content
        );
    }

    public function fromLocation(Location $location): Item
    {
        return Item::fromLocation(
            $this->locationGuesser,
            $this->siteaccessGuesser,
            $location
        );
    }
}
