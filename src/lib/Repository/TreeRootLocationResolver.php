<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Repository;

use Ibexa\Contracts\Core\MVC\EventSubscriber\ConfigScopeChangeSubscriber;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Resolves the tree root location and its excluded locations from the
 * `content.tree_root.location_id` and `content.tree_root.excluded_uri_prefixes` settings.
 *
 * @internal
 */
class TreeRootLocationResolver implements ConfigScopeChangeSubscriber, ResetInterface
{
    private LocationService $locationService;

    private URLAliasService $urlAliasService;

    private ConfigResolverInterface $configResolver;

    private LoggerInterface $logger;

    private ?Location $rootLocation = null;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location[]|null */
    private ?array $excludedLocations = null;

    public function __construct(
        LocationService $locationService,
        URLAliasService $urlAliasService,
        ConfigResolverInterface $configResolver,
        LoggerInterface $logger
    ) {
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
        $this->configResolver = $configResolver;
        $this->logger = $logger;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if the configured
     *         `content.tree_root.location_id` does not match an existing Location
     */
    public function resolveRootLocation(): Location
    {
        if ($this->rootLocation === null) {
            $treeRootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');
            $this->rootLocation = $this->locationService->loadLocation($treeRootLocationId);
        }

        return $this->rootLocation;
    }

    /**
     * Resolves `content.tree_root.excluded_uri_prefixes` entries into Locations.
     * Entries that can't be resolved to an existing Location are logged and skipped.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if a resolved
     *         URLAlias destination does not match an existing Location
     */
    public function resolveExcludedLocations(): array
    {
        if ($this->excludedLocations === null) {
            $this->excludedLocations = [];
            foreach ($this->configResolver->getParameter('content.tree_root.excluded_uri_prefixes') as $uriPrefix) {
                try {
                    $urlAlias = $this->urlAliasService->lookup($uriPrefix);
                    if ($urlAlias->type === URLAlias::LOCATION) {
                        $this->excludedLocations[] = $this->locationService->loadLocation($urlAlias->destination);
                    }
                } catch (NotFoundException|InvalidArgumentException $e) {
                    $this->logger->warning(
                        sprintf(
                            'Invalid content.tree_root.excluded_uri_prefixes entry "%s": %s',
                            $uriPrefix,
                            $e->getMessage()
                        ),
                        ['exception' => $e]
                    );
                }
            }
        }

        return $this->excludedLocations;
    }

    public function onConfigScopeChange(ScopeChangeEvent $event): void
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->rootLocation = null;
        $this->excludedLocations = null;
    }
}
