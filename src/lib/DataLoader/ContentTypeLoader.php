<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\DataLoader;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

/**
 * @internal
 */
interface ContentTypeLoader
{
    public function load($contentTypeId): ContentType;

    public function loadByIdentifier($identifier): ContentType;
}
