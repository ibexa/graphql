<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\Value\Item;

class ItemFactory
{
    /**
     * @var \Ibexa\GraphQL\Resolver\LocationGuesser\LocationGuesser
     */
    private $locationGuesser;
    /**
     * @var \Ibexa\GraphQL\Resolver\SiteaccessGuesser\SiteaccessGuesser
     */
    private $siteaccessGuesser;

    public function __construct(
        Resolver\LocationGuesser\LocationGuesser $locationGuesser,
        Resolver\SiteaccessGuesser\SiteaccessGuesser $siteaccessGuesser
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

class_alias(ItemFactory::class, 'EzSystems\EzPlatformGraphQL\GraphQL\ItemFactory');
