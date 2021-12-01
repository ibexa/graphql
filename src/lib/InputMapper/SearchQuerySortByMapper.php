<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\InputMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;

class SearchQuerySortByMapper
{
    /**
     * @param string[] $sortInput
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\URL\Query\SortClause[]
     */
    public function mapInputToSortClauses(array $sortInput)
    {
        $sortClauses = array_map(
            static function (string $sortClauseClass) {
                /** @var Query\SortClause $lastSortClause */
                static $lastSortClause;

                if ($sortClauseClass === Query::SORT_DESC) {
                    if (!$lastSortClause instanceof Query\SortClause) {
                        return null;
                    }

                    $lastSortClause->direction = $sortClauseClass;

                    return null;
                }

                if (!class_exists($sortClauseClass)) {
                    return null;
                }

                if (!in_array(Query\SortClause::class, class_parents($sortClauseClass))) {
                    return null;
                }

                return $lastSortClause = new $sortClauseClass();
            },
            $sortInput
        );

        return array_filter($sortClauses);
    }
}

class_alias(SearchQuerySortByMapper::class, 'EzSystems\EzPlatformGraphQL\GraphQL\InputMapper\SearchQuerySortByMapper');
