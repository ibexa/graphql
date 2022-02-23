<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\DataLoader;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

/**
 * @internal
 */
interface LocationLoader
{
    /**
     * Loads a list of locations given a Query Criterion.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    public function find(LocationQuery $query): array;

    /**
     * Loads a single content item given a Query Criterion.
     *
     * @param string $id a location id
     */
    public function findById($id): Location;

    /**
     * Loads a single location by remote id.
     *
     * @param string $remoteId A location remote id
     */
    public function findByRemoteId($remoteId): Location;

    /**
     * Loads a single location by url alias.
     */
    public function findByUrlAlias(string $urlAlias): Location;

    /**
     * Counts the results of a query.
     *
     * @return int
     */
    public function count(LocationQuery $query);
}

class_alias(LocationLoader::class, 'EzSystems\EzPlatformGraphQL\GraphQL\DataLoader\LocationLoader');
