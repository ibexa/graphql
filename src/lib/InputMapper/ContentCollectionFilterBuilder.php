<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\InputMapper;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Core\MVC\ConfigResolverInterface;

/**
 * Builds the base query used to retrieve locations collections.
 *
 * @internal
 */
class ContentCollectionFilterBuilder
{
    /**
     * @var \Ibexa\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Repository
     */
    private $repository;

    public function __construct(ConfigResolverInterface $configResolver, Repository $repository)
    {
        $this->configResolver = $configResolver;
        $this->repository = $repository;
    }

    /**
     * Returns a criterion to be added as a global 'and' to a query's filters.
     */
    public function buildFilter(): Criterion
    {
        $treeRootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');
        $rootLocation = $this->repository->getLocationService()->loadLocation($treeRootLocationId);

        $includedSubtrees = [$rootLocation->pathString ?? '/'];

        foreach ($this->configResolver->getParameter('content.tree_root.excluded_uri_prefixes') as $uriPrefix) {
            $urlAlias = $this->repository->getURLAliasService()->lookup($uriPrefix);
            if ($urlAlias->type === URLAlias::LOCATION) {
                $includedSubtrees[] = $this->repository->getLocationService()->loadLocation($urlAlias->destination)->pathString;
            }
        }

        return new Subtree($includedSubtrees);
    }
}

class_alias(ContentCollectionFilterBuilder::class, 'EzSystems\EzPlatformGraphQL\GraphQL\InputMapper\ContentCollectionFilterBuilder');
