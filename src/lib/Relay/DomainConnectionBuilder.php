<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Relay;

use Overblog\GraphQLBundle\Relay\Connection\ConnectionBuilder;

class DomainConnectionBuilder extends ConnectionBuilder
{
    public const PREFIX = 'DomainContent:';
}

class_alias(DomainConnectionBuilder::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Relay\DomainConnectionBuilder');
