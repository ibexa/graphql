<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Ibexa\GraphQL\Exception;

class LocationGuess
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    private $content;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location[]
     */
    private $locations;

    public function __construct(Content $content, array $locations)
    {
        $this->content = $content;
        $this->locations = $locations;
    }

    /**
     * Returns the location guess result if the guess was successful.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     *
     * @throws \EzSystems\EzPlatformGraphQL\Exception\MultipleValidLocationsException
     * @throws \EzSystems\EzPlatformGraphQL\Exception\NoValidLocationsException
     */
    public function getLocation(): Location
    {
        if (count($this->locations) > 1) {
            throw new Exception\MultipleValidLocationsException($this->content, $this->locations);
        } elseif (count($this->locations) === 0) {
            throw new Exception\NoValidLocationsException($this->content);
        }

        return $this->locations[0];
    }

    public function isSuccessful(): bool
    {
        return count($this->locations) === 1;
    }
}

class_alias(LocationGuess::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\LocationGuesser\LocationGuess');
