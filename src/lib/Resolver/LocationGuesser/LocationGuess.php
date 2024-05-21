<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\Exception;

class LocationGuess
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    private $content;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
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
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     *
     * @throws \Ibexa\GraphQL\Exception\MultipleValidLocationsException
     * @throws \Ibexa\GraphQL\Exception\NoValidLocationsException
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
