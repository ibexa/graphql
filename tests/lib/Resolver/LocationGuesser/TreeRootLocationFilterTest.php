<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\Resolver\LocationGuesser\LocationList;
use Ibexa\GraphQL\Resolver\LocationGuesser\TreeRootLocationFilter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class TreeRootLocationFilterTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService&\PHPUnit\Framework\MockObject\MockObject */
    private LocationService $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\URLAliasService&\PHPUnit\Framework\MockObject\MockObject */
    private URLAliasService $urlAliasService;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    private TreeRootLocationFilter $filter;

    protected function setUp(): void
    {
        $this->locationService = $this->createMock(LocationService::class);
        $this->urlAliasService = $this->createMock(URLAliasService::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);

        $this->filter = new TreeRootLocationFilter(
            $this->locationService,
            $this->urlAliasService,
            $this->configResolver,
            $this->createMock(LoggerInterface::class),
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testFilterRemovesLocationOutsideTreeRootWhenExcludedUriPrefixIsStale(): void
    {
        $rootLocation = new Location(['id' => 2, 'pathString' => '/1/2/']);
        $candidateLocation = new Location(['id' => 3, 'pathString' => '/1/3/']);

        $this->configResolver
            ->method('getParameter')
            ->willReturnMap([
                ['content.tree_root.location_id', null, null, 2],
                ['content.tree_root.excluded_uri_prefixes', null, null, ['/this-path-definitely-does-not-exist-12345']],
            ]);

        $this->locationService->method('loadLocation')->with(2)->willReturn($rootLocation);

        $this->urlAliasService
            ->method('lookup')
            ->with('/this-path-definitely-does-not-exist-12345')
            ->willThrowException(new NotFoundException('URLAlias', '/this-path-definitely-does-not-exist-12345'));

        $locationList = $this->createMock(LocationList::class);
        $locationList->method('getLocations')->willReturn([$candidateLocation]);
        $locationList->expects(self::once())->method('removeLocation')->with($candidateLocation);

        $this->filter->filter($this->createMock(Content::class), $locationList);
    }

    /**
     * @runInSeparateProcess
     */
    public function testFilterKeepsLocationUnderResolvedExcludedUriPrefix(): void
    {
        $rootLocation = new Location(['id' => 2, 'pathString' => '/1/2/']);
        $excludedLocation = new Location(['id' => 42, 'pathString' => '/1/42/']);
        $candidateLocation = new Location(['id' => 43, 'pathString' => '/1/42/43/']);

        $this->configResolver
            ->method('getParameter')
            ->willReturnMap([
                ['content.tree_root.location_id', null, null, 2],
                ['content.tree_root.excluded_uri_prefixes', null, null, ['/excluded']],
            ]);

        $this->locationService
            ->method('loadLocation')
            ->willReturnMap([
                [2, null, null, $rootLocation],
                [42, null, null, $excludedLocation],
            ]);

        $this->urlAliasService
            ->method('lookup')
            ->with('/excluded')
            ->willReturn(new URLAlias(['type' => URLAlias::LOCATION, 'destination' => 42]));

        $locationList = $this->createMock(LocationList::class);
        $locationList->method('getLocations')->willReturn([$candidateLocation]);
        $locationList->expects(self::never())->method('removeLocation');

        $this->filter->filter($this->createMock(Content::class), $locationList);
    }
}
