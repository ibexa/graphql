<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Relay;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Overblog\GraphQLBundle\Relay\Node\GlobalId;
use Overblog\GraphQLBundle\Resolver\TypeResolver;

class NodeResolver
{
    private ContentService $contentService;

    private TypeResolver $typeResolver;

    private ContentTypeService $contentTypeService;

    private NameHelper $nameHelper;

    public function __construct(ContentService $contentService, TypeResolver $typeResolver, ContentTypeService $contentTypeService, NameHelper $nameHelper)
    {
        $this->contentService = $contentService;
        $this->typeResolver = $typeResolver;
        $this->contentTypeService = $contentTypeService;
        $this->nameHelper = $nameHelper;
    }

    /**
     * @param $globalId
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|null
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function resolveNode($globalId)
    {
        $params = GlobalId::fromGlobalId($globalId);

        if (in_array($params['type'], ['Content', 'DomainContent'])) {
            return $this->contentService->loadContentInfo($params['id']);
        }

        return null;
    }

    /**
     * @param $object
     *
     * @return \GraphQL\Type\Definition\Type
     */
    public function resolveType($object)
    {
        if ($object instanceof ContentInfo) {
            return $this->typeResolver->resolve(
                $this->nameHelper->itemName(
                    $this->contentTypeService->loadContentType($object->contentTypeId)
                )
            );
        }
    }
}
