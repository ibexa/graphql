<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\DataLoader\Exception;

use eZ\Publish\API\Repository\Exceptions\NotFoundException as NotFoundApiException;

class NotFoundException extends NotFoundApiException
{
}

class_alias(NotFoundException::class, 'EzSystems\EzPlatformGraphQL\GraphQL\DataLoader\Exception\NotFoundException');
