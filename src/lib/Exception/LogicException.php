<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Exception;

class LogicException extends \LogicException
{
}

class_alias(LogicException::class, 'EzSystems\EzPlatformGraphQL\Exception\LogicException');
