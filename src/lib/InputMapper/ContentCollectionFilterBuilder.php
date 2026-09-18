<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\InputMapper;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Psr\Log\LoggerInterface;

/**
 * Builds the base query used to retrieve locations collections.
 *
 * @internal
 */
class ContentCollectionFilterBuilder
{
    /**
     * @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Repository
     */
    private $repository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(ConfigResolverInterface $configResolver, Repository $repository, LoggerInterface $logger)
    {
        $this->configResolver = $configResolver;
        $this->repository = $repository;
        $this->logger = $logger;
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
            try {
                $urlAlias = $this->repository->getURLAliasService()->lookup($uriPrefix);
                if ($urlAlias->type === URLAlias::LOCATION) {
                    $includedSubtrees[] = $this->repository->getLocationService()->loadLocation($urlAlias->destination)->pathString;
                }
            } catch (NotFoundException $e) {
                $this->logger->warning(
                    sprintf(
                        '[GraphQL] Invalid content.tree_root.excluded_uri_prefixes entry "%s": %s',
                        $uriPrefix,
                        $e->getMessage()
                    ),
                    ['exception' => $e]
                );
            }
        }

        return new Subtree($includedSubtrees);
    }
}

class_alias(ContentCollectionFilterBuilder::class, 'EzSystems\EzPlatformGraphQL\GraphQL\InputMapper\ContentCollectionFilterBuilder');
