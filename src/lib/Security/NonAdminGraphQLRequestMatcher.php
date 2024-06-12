<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Security;

use Ibexa\AdminUi\Specification\SiteAccess\IsAdmin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * Security request matcher that excludes admin+graphql requests.
 * Needed because the admin uses GraphQL without a JWT.
 */
final readonly class NonAdminGraphQLRequestMatcher implements RequestMatcherInterface
{
    /**
     * @param string[][] $siteAccessGroups
     */
    public function __construct(
        private array $siteAccessGroups
    ) {
    }

    /**
     * @throws \Ibexa\AdminUi\Exception\InvalidArgumentException
     */
    public function matches(Request $request): bool
    {
        return
            $request->attributes->get('_route') === 'overblog_graphql_endpoint' &&
            !$this->isAdminSiteAccess($request);
    }

    private function isAdminSiteAccess(Request $request): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'));
    }
}
