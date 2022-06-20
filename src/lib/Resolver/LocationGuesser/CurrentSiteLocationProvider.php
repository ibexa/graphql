<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Resolver\LocationGuesser;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\GraphQL\InputMapper\ContentCollectionFilterBuilder;

/**
 * Returns the locations from the current site (e.g. within its tree root).
 */
class CurrentSiteLocationProvider implements LocationProvider
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\SearchService
     */
    private $searchService;

    /**
     * @var \Ibexa\GraphQL\InputMapper\ContentCollectionFilterBuilder
     */
    private $filterBuilder;

    public function __construct(SearchService $searchService, ContentCollectionFilterBuilder $filterBuilder)
    {
        $this->searchService = $searchService;
        $this->filterBuilder = $filterBuilder;
    }

    public function getLocations(Content $content): LocationList
    {
        $query = new LocationQuery([
            'filter' => new Criterion\LogicalAnd([
                $this->filterBuilder->buildFilter(),
                new Criterion\ContentId($content->id),
            ]),
        ]);

        $list = new ObjectStorageLocationList($content);
        foreach ($this->searchService->findLocations($query)->searchHits as $searchHit) {
            $list->addLocation($searchHit->valueObject);
        }

        return $list;
    }
}

class_alias(CurrentSiteLocationProvider::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\LocationGuesser\CurrentSiteLocationProvider');
