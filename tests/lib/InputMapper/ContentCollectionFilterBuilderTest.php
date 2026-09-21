<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\GraphQL\InputMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\GraphQL\InputMapper\ContentCollectionFilterBuilder;
use Ibexa\GraphQL\Repository\TreeRootLocationResolver;
use PHPUnit\Framework\TestCase;

final class ContentCollectionFilterBuilderTest extends TestCase
{
    /** @var \Ibexa\GraphQL\Repository\TreeRootLocationResolver&\PHPUnit\Framework\MockObject\MockObject */
    private TreeRootLocationResolver $treeRootLocationResolver;

    private ContentCollectionFilterBuilder $filterBuilder;

    protected function setUp(): void
    {
        $this->treeRootLocationResolver = $this->createMock(TreeRootLocationResolver::class);
        $this->filterBuilder = new ContentCollectionFilterBuilder($this->treeRootLocationResolver);
    }

    public function testBuildFilterReturnsSubtreeCriterionForRootLocationOnly(): void
    {
        $this->treeRootLocationResolver
            ->method('resolveRootLocation')
            ->willReturn(new Location(['pathString' => '/1/2/']));
        $this->treeRootLocationResolver->method('resolveExcludedLocations')->willReturn([]);

        $filter = $this->filterBuilder->buildFilter();

        self::assertInstanceOf(Subtree::class, $filter);
        self::assertSame(['/1/2/'], $filter->value);
    }

    public function testBuildFilterIncludesResolvedExcludedLocations(): void
    {
        $this->treeRootLocationResolver
            ->method('resolveRootLocation')
            ->willReturn(new Location(['pathString' => '/1/2/']));
        $this->treeRootLocationResolver
            ->method('resolveExcludedLocations')
            ->willReturn([new Location(['pathString' => '/1/2/42/'])]);

        $filter = $this->filterBuilder->buildFilter();

        self::assertSame(['/1/2/', '/1/2/42/'], $filter->value);
    }
}
