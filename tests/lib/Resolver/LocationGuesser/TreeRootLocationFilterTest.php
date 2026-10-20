<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\Repository\TreeRootLocationResolverInterface;
use Ibexa\GraphQL\Resolver\LocationGuesser\LocationList;
use Ibexa\GraphQL\Resolver\LocationGuesser\TreeRootLocationFilter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class TreeRootLocationFilterTest extends TestCase
{
    /** @var TreeRootLocationResolverInterface&Stub */
    private TreeRootLocationResolverInterface $treeRootLocationResolver;

    private TreeRootLocationFilter $filter;

    protected function setUp(): void
    {
        $this->treeRootLocationResolver = $this->createStub(TreeRootLocationResolverInterface::class);
        $this->filter = new TreeRootLocationFilter($this->treeRootLocationResolver);
    }

    public function testFilterRemovesLocationOutsideTreeRootAndOutsideExcludedLocations(): void
    {
        $this->treeRootLocationResolver
            ->method('resolveRootLocation')
            ->willReturn(new Location(['id' => 2, 'pathString' => '/1/2/']));
        $this->treeRootLocationResolver
            ->method('resolveExcludedLocations')
            ->willReturn([]);

        $candidateLocation = new Location(['id' => 3, 'pathString' => '/1/3/']);

        $locationList = $this->createMock(LocationList::class);
        $locationList
            ->expects(self::once())
            ->method('getLocations')
            ->willReturn([$candidateLocation]);
        $locationList
            ->expects(self::once())
            ->method('removeLocation')
            ->with($candidateLocation);

        $this->filter->filter($this->createStub(Content::class), $locationList);
    }

    public function testFilterKeepsLocationUnderExcludedLocation(): void
    {
        $this->treeRootLocationResolver
            ->method('resolveRootLocation')
            ->willReturn(new Location(['id' => 2, 'pathString' => '/1/2/']));
        $this->treeRootLocationResolver
            ->method('resolveExcludedLocations')
            ->willReturn([new Location(['id' => 42, 'pathString' => '/1/42/'])]);

        $candidateLocation = new Location(['id' => 43, 'pathString' => '/1/42/43/']);

        $locationList = $this->createMock(LocationList::class);
        $locationList
            ->expects(self::once())
            ->method('getLocations')
            ->willReturn([$candidateLocation]);
        $locationList
            ->expects(self::never())
            ->method('removeLocation');

        $this->filter->filter($this->createStub(Content::class), $locationList);
    }
}
