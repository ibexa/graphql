<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\GraphQL\Value\Item;
use Overblog\GraphQLBundle\Resolver\TypeResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @internal
 */
class UrlAliasResolver
{
    private URLAliasService $urlAliasService;

    private TypeResolver $typeResolver;

    private ConfigResolverInterface $configResolver;

    private LocationService $locationService;

    private UrlAliasGenerator $urlGenerator;

    /**
     * @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessService
     */
    private SiteAccessServiceInterface $siteaccessService;

    public function __construct(
        TypeResolver $typeResolver,
        URLAliasService $urlAliasService,
        LocationService $locationService,
        ConfigResolverInterface $configResolver,
        UrlAliasGenerator $urlGenerator,
        SiteAccessServiceInterface $siteAccessService
    ) {
        $this->urlAliasService = $urlAliasService;
        $this->typeResolver = $typeResolver;
        $this->configResolver = $configResolver;
        $this->locationService = $locationService;
        $this->urlGenerator = $urlGenerator;
        $this->siteaccessService = $siteAccessService;
    }

    /**
     * @param array{custom ?: bool} $args
     *
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\URLAlias>
     */
    public function resolveLocationUrlAliases(Location $location, $args): iterable
    {
        return $this->urlAliasService->listLocationAliases(
            $location,
            isset($args['custom']) ? $args['custom'] : false
        );
    }

    public function resolveUrlAliasType(URLAlias $urlAlias): string
    {
        switch ($urlAlias->type) {
            case URLAlias::LOCATION:
                return $this->typeResolver->resolve('LocationUrlAlias');
            case URLAlias::RESOURCE:
                return $this->typeResolver->resolve('ResourceUrlAlias');
            case URLAlias::VIRTUAL:
                return $this->typeResolver->resolve('VirtualUrlAlias');
            default:
                return '';
        }
    }

    public function resolveLocationUrlAlias(Location $location): ?string
    {
        return $this->urlGenerator->generate($location, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Resolves the URL alias for an item, taking into account the item's siteaccess.
     */
    public function resolveItemUrlAlias(Item $item): ?string
    {
        if ($item->getSiteaccess() === $this->siteaccessService->getCurrent()) {
            $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH;
        } else {
            $referenceType = UrlGeneratorInterface::ABSOLUTE_URL;
        }

        return $this->urlGenerator->generate(
            $item->getLocation(),
            ['siteaccess' => $item->getSiteaccess()->name],
            $referenceType
        );
    }
}
