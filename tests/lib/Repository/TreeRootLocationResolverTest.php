<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\GraphQL\Repository;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\Repository\TreeRootLocationResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class TreeRootLocationResolverTest extends TestCase
{
    /** @var LocationService&MockObject */
    private LocationService $locationService;

    /** @var URLAliasService&MockObject */
    private URLAliasService $urlAliasService;

    /** @var ConfigResolverInterface&Stub */
    private ConfigResolverInterface $configResolver;

    /** @var LoggerInterface&MockObject */
    private LoggerInterface $logger;

    private TreeRootLocationResolver $resolver;

    protected function setUp(): void
    {
        $this->locationService = $this->createMock(LocationService::class);
        $this->urlAliasService = $this->createMock(URLAliasService::class);
        $this->configResolver = $this->createStub(ConfigResolverInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->resolver = new TreeRootLocationResolver(
            $this->locationService,
            $this->urlAliasService,
            $this->configResolver,
            $this->logger,
        );
    }

    public function testResolveRootLocationLoadsAndCachesConfiguredLocation(): void
    {
        $rootLocation = new Location(['id' => 2]);

        $this->configResolver
            ->method('getParameter')
            ->with('content.tree_root.location_id')
            ->willReturn(2);

        $this->locationService
            ->expects(self::once())
            ->method('loadLocation')
            ->with(2)
            ->willReturn($rootLocation);

        self::assertSame($rootLocation, $this->resolver->resolveRootLocation());
        self::assertSame($rootLocation, $this->resolver->resolveRootLocation());
    }

    public function testResolveExcludedLocationsSkipsAndLogsStaleUriPrefix(): void
    {
        $this->configResolver
            ->method('getParameter')
            ->with('content.tree_root.excluded_uri_prefixes')
            ->willReturn(['/this-path-definitely-does-not-exist-12345']);

        $this->urlAliasService
            ->expects(self::once())
            ->method('lookup')
            ->with('/this-path-definitely-does-not-exist-12345')
            ->willThrowException(new NotFoundException('URLAlias', '/this-path-definitely-does-not-exist-12345'));

        $this->logger->expects(self::once())->method('warning');

        self::assertSame([], $this->resolver->resolveExcludedLocations());
    }

    public function testResolveExcludedLocationsSkipsAndLogsUriPrefixExceedingMaxDepth(): void
    {
        $this->configResolver
            ->method('getParameter')
            ->with('content.tree_root.excluded_uri_prefixes')
            ->willReturn(['/too/deep/to/be/a/valid/uri/alias']);

        $this->urlAliasService
            ->expects(self::once())
            ->method('lookup')
            ->with('/too/deep/to/be/a/valid/uri/alias')
            ->willThrowException(new InvalidArgumentException('url', 'path exceeds maximum depth level'));

        $this->logger->expects(self::once())->method('warning');

        self::assertSame([], $this->resolver->resolveExcludedLocations());
    }

    public function testResolveExcludedLocationsResolvesUriPrefixToLocationAndCachesResult(): void
    {
        $excludedLocation = new Location(['id' => 42]);

        $this->configResolver
            ->method('getParameter')
            ->with('content.tree_root.excluded_uri_prefixes')
            ->willReturn(['/excluded']);

        $this->urlAliasService
            ->expects(self::once())
            ->method('lookup')
            ->with('/excluded')
            ->willReturn(new URLAlias(['type' => URLAlias::LOCATION, 'destination' => 42]));

        $this->locationService
            ->expects(self::once())
            ->method('loadLocation')
            ->with(42)
            ->willReturn($excludedLocation);

        self::assertSame([$excludedLocation], $this->resolver->resolveExcludedLocations());
        self::assertSame([$excludedLocation], $this->resolver->resolveExcludedLocations());
    }

    /**
     * @dataProvider provideCacheResetTriggers
     *
     * @param callable(TreeRootLocationResolver): void $resetCache
     */
    public function testCacheResetClearsCachedRootLocationAndExcludedLocations(callable $resetCache): void
    {
        $firstRootLocation = new Location(['id' => 2]);
        $firstExcludedLocation = new Location(['id' => 42]);
        $secondRootLocation = new Location(['id' => 2]);
        $secondExcludedLocation = new Location(['id' => 42]);

        $this->configResolver
            ->method('getParameter')
            ->willReturnMap([
                ['content.tree_root.location_id', null, null, 2],
                ['content.tree_root.excluded_uri_prefixes', null, null, ['/excluded']],
            ]);

        $this->urlAliasService
            ->expects(self::exactly(2))
            ->method('lookup')
            ->with('/excluded')
            ->willReturn(new URLAlias(['type' => URLAlias::LOCATION, 'destination' => 42]));

        $this->locationService
            ->expects(self::exactly(4))
            ->method('loadLocation')
            ->willReturnOnConsecutiveCalls(
                $firstRootLocation,
                $firstExcludedLocation,
                $secondRootLocation,
                $secondExcludedLocation,
            );

        self::assertSame($firstRootLocation, $this->resolver->resolveRootLocation());
        self::assertSame([$firstExcludedLocation], $this->resolver->resolveExcludedLocations());

        $resetCache($this->resolver);

        self::assertSame($secondRootLocation, $this->resolver->resolveRootLocation());
        self::assertSame([$secondExcludedLocation], $this->resolver->resolveExcludedLocations());
    }

    /**
     * @return iterable<string, array{callable(TreeRootLocationResolver): void}>
     */
    public static function provideCacheResetTriggers(): iterable
    {
        yield 'kernel reset' => [
            static function (TreeRootLocationResolver $resolver): void {
                $resolver->reset();
            },
        ];

        yield 'config scope change' => [
            static function (TreeRootLocationResolver $resolver): void {
                $resolver->onConfigScopeChange(new ScopeChangeEvent(new SiteAccess('test')));
            },
        ];
    }
}
