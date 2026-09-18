<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\GraphQL\InputMapper;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\InputMapper\ContentCollectionFilterBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ContentCollectionFilterBuilderTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject */
    private Repository $repository;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService&\PHPUnit\Framework\MockObject\MockObject */
    private LocationService $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\URLAliasService&\PHPUnit\Framework\MockObject\MockObject */
    private URLAliasService $urlAliasService;

    /** @var \Psr\Log\LoggerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private LoggerInterface $logger;

    private ContentCollectionFilterBuilder $filterBuilder;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->locationService = $this->createMock(LocationService::class);
        $this->urlAliasService = $this->createMock(URLAliasService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->repository = $this->createMock(Repository::class);
        $this->repository->method('getLocationService')->willReturn($this->locationService);
        $this->repository->method('getURLAliasService')->willReturn($this->urlAliasService);

        $this->filterBuilder = new ContentCollectionFilterBuilder(
            $this->configResolver,
            $this->repository,
            $this->logger,
        );
    }

    public function testBuildFilterIgnoresStaleExcludedUriPrefix(): void
    {
        $rootLocation = new Location(['pathString' => '/1/2/']);

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

        $filter = $this->filterBuilder->buildFilter();

        self::assertInstanceOf(Subtree::class, $filter);
        self::assertSame(['/1/2/'], $filter->value);
    }

    public function testBuildFilterIncludesResolvedExcludedUriPrefix(): void
    {
        $rootLocation = new Location(['pathString' => '/1/2/']);
        $excludedLocation = new Location(['pathString' => '/1/2/42/']);

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

        $filter = $this->filterBuilder->buildFilter();

        self::assertSame(['/1/2/', '/1/2/42/'], $filter->value);
    }
}
