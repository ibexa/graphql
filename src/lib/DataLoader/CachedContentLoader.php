<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\DataLoader;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

/**
 * @internal
 */
class CachedContentLoader implements ContentLoader
{
    /**
     * @var \Ibexa\GraphQL\DataLoader\ContentLoader
     */
    private $innerLoader;

    private $loadedItems = [];

    public function __construct(ContentLoader $innerLoader)
    {
        $this->innerLoader = $innerLoader;
    }

    public function find(Query $query): array
    {
        $items = $this->innerLoader->find($query);

        foreach ($items as $item) {
            $this->loadedItems[$item->id] = $item;
        }

        return $items;
    }

    public function findSingle(Criterion $filter): Content
    {
        $contentId = $filter->value[0];
        if ($filter instanceof Criterion\ContentId && isset($this->loadedItems[$contentId])) {
            return $this->loadedItems[$contentId];
        }

        $item = $this->innerLoader->findSingle($filter);
        $this->loadedItems[$item->id] = $item;

        return $item;
    }

    /**
     * Counts the results of a query.
     *
     * @return int
     */
    public function count(Query $query)
    {
        return $this->innerLoader->count($query);
    }
}

class_alias(CachedContentLoader::class, 'EzSystems\EzPlatformGraphQL\GraphQL\DataLoader\CachedContentLoader');
