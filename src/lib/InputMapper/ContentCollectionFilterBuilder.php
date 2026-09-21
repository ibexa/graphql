<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\InputMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\GraphQL\Repository\TreeRootLocationResolver;

/**
 * Builds the base query used to retrieve locations collections.
 *
 * @internal
 */
class ContentCollectionFilterBuilder
{
    private TreeRootLocationResolver $treeRootLocationResolver;

    public function __construct(TreeRootLocationResolver $treeRootLocationResolver)
    {
        $this->treeRootLocationResolver = $treeRootLocationResolver;
    }

    /**
     * Returns a criterion to be added as a global 'and' to a query's filters.
     */
    public function buildFilter(): Criterion
    {
        $rootLocation = $this->treeRootLocationResolver->resolveRootLocation();

        $includedSubtrees = [$rootLocation->pathString ?? '/'];

        foreach ($this->treeRootLocationResolver->resolveExcludedLocations() as $excludedLocation) {
            $includedSubtrees[] = $excludedLocation->pathString;
        }

        return new Subtree($includedSubtrees);
    }
}

class_alias(ContentCollectionFilterBuilder::class, 'EzSystems\EzPlatformGraphQL\GraphQL\InputMapper\ContentCollectionFilterBuilder');
