<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\Resolver\LocationGuesser\FilterLocationGuesser;
use Ibexa\GraphQL\Resolver\LocationGuesser\LocationFilter;
use Ibexa\GraphQL\Resolver\LocationGuesser\LocationGuess;
use Ibexa\GraphQL\Resolver\LocationGuesser\LocationGuesser;
use Ibexa\GraphQL\Resolver\LocationGuesser\LocationList;
use Ibexa\GraphQL\Resolver\LocationGuesser\LocationProvider;
use Ibexa\GraphQL\Resolver\LocationGuesser\ObjectStorageLocationList;
use PhpSpec\ObjectBehavior;

class FilterLocationGuesserSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FilterLocationGuesser::class);
        $this->shouldHaveType(LocationGuesser::class);
    }

    public function let(LocationProvider $locationProvider, LocationFilter $locationFilter, LocationFilter $otherLocationFilter): void
    {
        $this->beConstructedWith($locationProvider, [$locationFilter, $otherLocationFilter]);
    }

    public function it_gets_the_initial_location_list_from_the_provider(LocationProvider $locationProvider): void
    {
        $content = new Content();
        $locationProvider->getLocations($content)->willReturn(new ObjectStorageLocationList($content));
        $this->guessLocation($content);
    }

    public function it_does_not_filter_if_there_is_only_one_location(LocationProvider $locationProvider, LocationFilter $locationFilter): void
    {
        $content = new Content();
        $location = new Location();
        $locationList = new ObjectStorageLocationList($content);
        $locationList->addLocation($location);

        $locationProvider->getLocations($content)->willReturn($locationList);

        $locationFilter->filter($content, $locationList)->shouldNotBeCalled();
        $this->guessLocation($content)->shouldBeLike(new LocationGuess($content, [$location]));
    }

    public function it_returns_as_soon_as_there_is_one_location_left(
        LocationProvider $locationProvider,
        LocationFilter $locationFilter,
        LocationFilter $secondLocationFilter,
        LocationList $locationList
    ): void {
        $content = new Content();
        $firstLocation = new Location();
        $secondLocation = new Location();

        $locationProvider->getLocations($content)->willReturn($locationList);
        $locationList->hasOneLocation()->willReturn(false);
        $locationList->getLocations()->willReturn([$firstLocation]);
        $locationFilter->filter($content, $locationList)->will(static function ($args) use ($locationList): void {
            $locationList->hasOneLocation()->willReturn(true);
        });

        $secondLocationFilter->filter($content, $locationList)->shouldNotBeCalled();

        $this->guessLocation($content)->shouldGuess($firstLocation);
    }

    public function getMatchers(): array
    {
        return [
            'guess' => static function (LocationGuess $subject, Location $location): bool {
                return $subject->isSuccessful() && $subject->getLocation() === $location;
            },
        ];
    }
}
