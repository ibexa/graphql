<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\GraphQL\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher;

final class HeaderAuthorizationGraphQLRequestMatcher extends RequestMatcher
{
    public function matches(Request $request): bool
    {
        return
            $request->attributes->get('_route') === 'overblog_graphql_endpoint'
            && $request->headers->has('Authorization')
            && parent::matches($request);
    }
}
