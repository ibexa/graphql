<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\DataLoader\Exception;

use Exception;

class ArgumentsException extends Exception
{
}

class_alias(ArgumentsException::class, 'EzSystems\EzPlatformGraphQL\GraphQL\DataLoader\Exception\ArgumentsException');
