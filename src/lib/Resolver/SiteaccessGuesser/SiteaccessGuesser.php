<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Resolver\SiteaccessGuesser;

use Ibexa\AdminUi\Specification\SiteAccess\IsAdmin;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessProviderInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\GraphQL\Exception\NoValidSiteaccessException;

class SiteaccessGuesser
{
    private SiteAccessProviderInterface $provider;

    private ConfigResolverInterface $configResolver;

    private SiteAccessServiceInterface $siteAccessService;

    /** @var array<string, string> */
    private array $siteAccessGroups;

    /**
     * @param array<string, string> $siteAccessGroups
     */
    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        SiteAccessProviderInterface $provider,
        ConfigResolverInterface $configResolver,
        array $siteAccessGroups
    ) {
        $this->provider = $provider;
        $this->configResolver = $configResolver;
        $this->siteAccessService = $siteAccessService;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    /**
     * @throws \Ibexa\GraphQL\Exception\NoValidSiteaccessException
     */
    public function guessForLocation(Location $location): SiteAccess
    {
        // Test if the location is part of the current tree root, as it is the most likely
        $treeRootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');
        if ($this->getDepthInSubtree($location, $treeRootLocationId) !== false) {
            return $this->siteAccessService->getCurrent() ?? throw new NoValidSiteaccessException($location);
        }

        // we won't look into SiteAccess-es that don't use the same repository
        $currentRepository = $this->configResolver->getParameter('repository');

        $matchingSiteAccessRootDepth = 0;
        /** @var \Ibexa\Core\MVC\Symfony\SiteAccess[] $saList */
        $saList = iterator_to_array($this->provider->getSiteAccesses());

        foreach ($saList as $siteAccess) {
            $repository = $this->configResolver->getParameter('repository', 'ibexa.site_access.config', $siteAccess->name);

            if ($repository !== $currentRepository || $this->isAdminSiteAccess($siteAccess)) {
                continue;
            }

            $treeRootLocationId = $this->configResolver->getParameter(
                'content.tree_root.location_id',
                'ibexa.site_access.config',
                $siteAccess->name
            );
            $rootDepth = $this->getDepthInSubtree($location, $treeRootLocationId);
            if ($rootDepth !== false && $rootDepth > $matchingSiteAccessRootDepth) {
                $matchingSiteAccess = $siteAccess;
                $matchingSiteAccessRootDepth = $rootDepth;
            }
        }

        if (!isset($matchingSiteAccess)) {
            throw new NoValidSiteaccessException($location);
        }

        return $matchingSiteAccess;
    }

    /**
     * Tests if $location is part of a subtree, and returns the root depth.
     *
     * @return int|false The root depth (used to select the deepest, most specific tree root), false if it isn't part of that subtree.
     */
    private function getDepthInSubtree(Location $location, int $treeRootLocationId): int|false
    {
        return array_search($treeRootLocationId, array_map('intval', $location->getPath()), true);
    }

    private function isAdminSiteAccess(SiteAccess $siteAccess): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($siteAccess);
    }
}
