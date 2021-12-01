<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\GraphQL\Exception\NoValidLocationsException;
use Ibexa\GraphQL\Resolver\LocationGuesser\LocationGuesser;
use Ibexa\GraphQL\Resolver\SiteaccessGuesser\SiteaccessGuesser;
use Overblog\GraphQLBundle\Error\UserError;

/**
 * A DXP item, combination of a Content and Location.
 */
class Item
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location;

    /** @var \Ibexa\GraphQL\Resolver\LocationGuesser\LocationGuesser */
    private $locationGuesser;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess */
    private $siteaccess;

    /** @var \Ibexa\GraphQL\Resolver\SiteaccessGuesser\SiteaccessGuesser */
    private $siteaccessGuesser;

    private function __construct(LocationGuesser $locationGuesser, SiteaccessGuesser $siteaccessGuesser, ?Location $location = null, ?Content $content = null)
    {
        if ($location === null && $content === null) {
            throw new InvalidArgumentException('content or location', 'one of content or location is required');
        }
        $this->location = $location;
        $this->content = $content;
        $this->locationGuesser = $locationGuesser;
        $this->siteaccessGuesser = $siteaccessGuesser;
    }

    public function getContent(): Content
    {
        if ($this->content === null) {
            $this->content = $this->location->getContent();
        }

        return $this->content;
    }

    public function getLocation(): Location
    {
        if ($this->location === null) {
            try {
                $this->location = $this->locationGuesser->guessLocation($this->content)->getLocation();
            } catch (NoValidLocationsException $e) {
                throw new UserError($e->getMessage(), 0, $e);
            }
        }

        return $this->location;
    }

    public function getContentInfo(): ContentInfo
    {
        return $this->getContent()->contentInfo;
    }

    public static function fromContent(LocationGuesser $locationGuesser, SiteaccessGuesser $siteaccessGuesser, Content $content): self
    {
        return new self($locationGuesser, $siteaccessGuesser, null, $content);
    }

    public static function fromLocation(LocationGuesser $locationGuesser, SiteaccessGuesser $siteaccessGuesser, Location $location): self
    {
        return new self($locationGuesser, $siteaccessGuesser, $location, null);
    }

    public function getSiteaccess(): SiteAccess
    {
        if ($this->siteaccess === null) {
            $this->siteaccess = $this->siteaccessGuesser->guessForLocation($this->getLocation());
        }

        return $this->siteaccess;
    }
}

class_alias(Item::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Value\Item');
