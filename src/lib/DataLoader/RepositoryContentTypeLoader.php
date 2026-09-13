<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\DataLoader;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

/**
 * @internal
 */
class RepositoryContentTypeLoader implements ContentTypeLoader
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function load($contentTypeId): ContentType
    {
        return $this->contentTypeService->loadContentType($contentTypeId);
    }

    public function loadByIdentifier($identifier): ContentType
    {
        return $this->contentTypeService->loadContentTypeByIdentifier($identifier);
    }
}

class_alias(RepositoryContentTypeLoader::class, 'EzSystems\EzPlatformGraphQL\GraphQL\DataLoader\RepositoryContentTypeLoader');
