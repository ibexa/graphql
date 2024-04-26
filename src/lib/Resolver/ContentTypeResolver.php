<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Overblog\GraphQLBundle\Resolver\TypeResolver;

/**
 * @internal
 */
class ContentTypeResolver
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var \Overblog\GraphQLBundle\Resolver\TypeResolver
     */
    private $typeResolver;

    public function __construct(TypeResolver $typeResolver, ContentTypeService $contentTypeService)
    {
        $this->typeResolver = $typeResolver;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[]
     */
    public function resolveContentTypesFromGroup($args)
    {
        if (isset($args['groupId'])) {
            $group = $this->contentTypeService->loadContentTypeGroup($args['groupId']);
        }

        if (isset($args['groupIdentifier'])) {
            $group = $this->contentTypeService->loadContentTypeGroupByIdentifier($args['groupIdentifier']);
        }

        if (isset($group)) {
            $contentTypes = $this->contentTypeService->loadContentTypes($group);
        } else {
            $contentTypes = [];
            foreach ($this->contentTypeService->loadContentTypeGroups() as $group) {
                $contentTypes = array_merge(
                    $contentTypes,
                    $this->contentTypeService->loadContentTypes($group)
                );
            }
        }

        return $contentTypes;
    }

    public function resolveContentTypeById($contentTypeId)
    {
        return $this->contentTypeService->loadContentType($contentTypeId);
    }

    public function resolveContentTypeGroupByIdentifier($identifier)
    {
        return $this->contentTypeService->loadContentTypeGroupByIdentifier($identifier);
    }

    public function resolveContentType($args)
    {
        if (isset($args['id'])) {
            return $this->resolveContentTypeById($args['id']);
        }

        if (isset($args['identifier'])) {
            return $this->contentTypeService->loadContentTypeByIdentifier($args['identifier']);
        }
    }
}

class_alias(ContentTypeResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\ContentTypeResolver');
