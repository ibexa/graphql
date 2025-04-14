<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use GraphQL\Error\UserError;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\Content\RelationList\RelationListItemInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

/**
 * @internal
 */
class ContentResolver implements QueryInterface
{
    private ContentService $contentService;

    private SearchService $searchService;

    public function __construct(ContentService $contentService, SearchService $searchService)
    {
        $this->contentService = $contentService;
        $this->searchService = $searchService;
    }

    /**
     * @param int $contentTypeId
     *
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo>
     */
    public function findContentByType($contentTypeId): array
    {
        $searchResults = $this->searchService->findContentInfo(
            new Query([
                'filter' => new Query\Criterion\ContentTypeId($contentTypeId),
            ])
        );

        return array_map(
            static function (SearchHit $searchHit) {
                return $searchHit->valueObject;
            },
            $searchResults->searchHits
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Relation[]
     */
    public function findContentRelations(ContentInfo $contentInfo, ?int $version = null): array
    {
        return array_filter(array_map(
            static fn (RelationListItemInterface $relationListItem): ?Relation => $relationListItem->getRelation(),
            $this->contentService->loadRelationList(
                $this->contentService->loadVersionInfo($contentInfo, $version)
            )->items
        ));
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Relation>
     */
    public function findContentReverseRelations(ContentInfo $contentInfo): iterable
    {
        return $this->contentService->loadReverseRelations($contentInfo);
    }

    public function resolveContent($args): ContentInfo
    {
        if (isset($args['id'])) {
            return $this->contentService->loadContentInfo($args['id']);
        }

        if (isset($args['remoteId'])) {
            return $this->contentService->loadContentInfoByRemoteId($args['remoteId']);
        }

        throw new UserError('Either id or remoteId is required as an argument');
    }

    public function resolveContentById(int $contentId): ContentInfo
    {
        return $this->contentService->loadContentInfo($contentId);
    }

    /**
     * @param array<int> $contentIdList
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo>
     */
    public function resolveContentByIdList(array $contentIdList): array
    {
        try {
            $searchResults = $this->searchService->findContentInfo(
                new Query([
                    'filter' => new Query\Criterion\ContentId($contentIdList),
                ])
            );
        } catch (\Exception $e) {
            return [];
        }

        return array_map(
            static function (SearchHit $searchHit) {
                return $searchHit->valueObject;
            },
            $searchResults->searchHits
        );
    }

    /**
     * @param int $contentId
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo>
     */
    public function resolveContentVersions(int $contentId): iterable
    {
        return $this->contentService->loadVersions(
            $this->contentService->loadContentInfo($contentId)
        );
    }

    public function resolveCurrentVersion(ContentInfo $contentInfo): VersionInfo
    {
        return $this->contentService->loadVersionInfo($contentInfo);
    }
}
