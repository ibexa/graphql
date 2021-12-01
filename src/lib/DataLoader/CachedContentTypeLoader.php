<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\DataLoader;

use Ibexa\GraphQL\DataLoader\ContentTypeLoader;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

/**
 * @internal
 */
class CachedContentTypeLoader implements ContentTypeLoader
{
    /**
     * @var \Ibexa\GraphQL\DataLoader\ContentTypeLoader
     */
    private $innerLoader;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[]
     */
    private $loadedItems = [];

    public function __construct(ContentTypeLoader $innerLoader)
    {
        $this->innerLoader = $innerLoader;
    }

    public function load($contentTypeId): ContentType
    {
        if (!isset($this->loadedItems[$contentTypeId])) {
            $this->loadedItems[$contentTypeId] = $this->innerLoader->load($contentTypeId);
        }

        return $this->loadedItems[$contentTypeId];
    }

    public function loadByIdentifier($identifier): ContentType
    {
        $contentType = $this->innerLoader->loadByIdentifier($identifier);

        if (!isset($this->innerLoader[$contentType->id])) {
            $this->innerLoader[$contentType->id] = $contentType;
        }

        return $contentType;
    }
}

class_alias(CachedContentTypeLoader::class, 'EzSystems\EzPlatformGraphQL\GraphQL\DataLoader\CachedContentTypeLoader');
