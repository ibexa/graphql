<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\InputMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\GraphQL\InputMapper\ContentCollectionFilterBuilder;
use Ibexa\GraphQL\InputMapper\SearchQueryMapper;
use PhpSpec\ObjectBehavior;

class SearchQueryMapperSpec extends ObjectBehavior
{
    public function let(ContentCollectionFilterBuilder $filterBuilder): void
    {
        $this->beConstructedWith($filterBuilder);

        $filterBuilder->buildFilter()->willReturn(new Subtree('/1/2/'));
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SearchQueryMapper::class);
    }

    public function it_maps_ContentTypeIdentifier_to_a_ContentTypeIdentifier_criterion(): void
    {
        $this->mapInputToQuery(['ContentTypeIdentifier' => ['article']])->shouldFilterByContentType(['article']);
    }

    public function it_maps_Text_to_a_FullText_criterion(): void
    {
        $this
            ->mapInputToQuery(['Text' => 'graphql'])
            ->shouldFilterByFullText('graphql');
    }

    public function it_maps_Modified_before_to_a_created_lte_DateMetaData_criterion(): void
    {
        $this
            ->mapInputToQuery(['Modified' => ['before' => '1977/05/04']])
            ->shouldFilterByDateModified(Query\Criterion\Operator::LTE, '1977/05/04');
    }

    public function it_maps_Modified_on_to_a_created_eq_DateMetaData_criterion(): void
    {
        $this
            ->mapInputToQuery(['Modified' => ['on' => '1977/05/04']])
            ->shouldFilterByDateModified(Query\Criterion\Operator::EQ, '1977/05/04');
    }

    public function it_maps_Modified_after_to_a_created_gte_DateMetaData_criterion(): void
    {
        $this
            ->mapInputToQuery(['Modified' => ['after' => '1977/05/04']])
            ->shouldFilterByDateModified(Query\Criterion\Operator::GTE, '1977/05/04');
    }

    public function it_maps_Created_before_to_a_created_lte_DateMetaData_criterion(): void
    {
        $this
            ->mapInputToQuery(['Created' => ['before' => '1977/05/04']])
            ->shouldFilterByDateCreated(Query\Criterion\Operator::LTE, '1977/05/04');
    }

    public function it_maps_Created_on_to_a_created_eq_DateMetaData_criterion(): void
    {
        $this
            ->mapInputToQuery(['Created' => ['on' => '1977/05/04']])
            ->shouldFilterByDateCreated(Query\Criterion\Operator::EQ, '1977/05/04');
    }

    public function it_maps_Created_after_to_a_created_gte_DateMetaData_criterion(): void
    {
        $this
            ->mapInputToQuery(['Created' => ['after' => '1977/05/04']])
            ->shouldFilterByDateCreated(Query\Criterion\Operator::GTE, '1977/05/04');
    }

    public function it_maps_Field_to_a_Field_criterion(): void
    {
        $this
            ->mapInputToQuery(['Field' => ['target' => 'target_field', 'eq' => 'bar']])
            ->shouldFilterByField('target_field');
    }

    public function it_maps_Field_target_to_the_Field_criterion_target(): void
    {
        $this
            ->mapInputToQuery(['Field' => ['target' => 'target_field', 'eq' => 'bar']])
            ->shouldFilterByField('target_field', Query\Criterion\Operator::EQ, 'bar');
    }

    public function it_maps_Field_with_value_at_operator_key_to_the_Field_criterion_value(): void
    {
        $this
            ->mapInputToQuery(['Field' => ['target' => 'target_field', 'eq' => 'bar']])
            ->shouldFilterByField('target_field', null, 'bar');
    }

    public function it_maps_Field_operator_eq_to_Field_criterion_operator_EQ(): void
    {
        $this
            ->mapInputToQuery(['Field' => ['target' => 'target_field', 'eq' => 'bar']])
            ->shouldFilterByFieldWithOperator(Query\Criterion\Operator::EQ);
    }

    public function it_maps_Field_operator_in_to_Field_criterion_operator_IN(): void
    {
        $this
            ->mapInputToQuery(['Field' => ['target' => 'target_field', 'eq' => 'bar']])
            ->shouldFilterByFieldWithOperator(Query\Criterion\Operator::EQ);
    }

    public function it_maps_Field_operator_lt_to_Field_criterion_operator_LT(): void
    {
        $this
            ->mapInputToQuery(['Field' => ['target' => 'target_field', 'lt' => 'bar']])
            ->shouldFilterByFieldWithOperator(Query\Criterion\Operator::LT);
    }

    public function it_maps_Field_operator_lte_to_Field_criterion_operator_LTE(): void
    {
        $this
            ->mapInputToQuery(['Field' => ['target' => 'target_field', 'lte' => 'bar']])
            ->shouldFilterByFieldWithOperator(Query\Criterion\Operator::LTE);
    }

    public function it_maps_Field_operator_gte_to_Field_criterion_operator_GTE(): void
    {
        $this
            ->mapInputToQuery(['Field' => ['target' => 'target_field', 'gte' => 'bar']])
            ->shouldFilterByFieldWithOperator(Query\Criterion\Operator::GTE);
    }

    public function it_maps_Field_operator_gt_to_Field_criterion_operator_GT(): void
    {
        $this
            ->mapInputToQuery(['Field' => ['target' => 'target_field', 'gt' => 'bar']])
            ->shouldFilterByFieldWithOperator(Query\Criterion\Operator::GT);
    }

    public function it_maps_Field_operator_between_to_Field_criterion_operator_BETWEEN(): void
    {
        $this
            ->mapInputToQuery(['Field' => ['target' => 'target_field', 'between' => [10, 20]]])
            ->shouldFilterByFieldWithOperator(Query\Criterion\Operator::BETWEEN);
    }

    public function getMatchers(): array
    {
        return [
            'filterByContentType' => function (Query $query, array $contentTypes): bool {
                $criterion = $this->findCriterionInQueryFilter(
                    $query,
                    Query\Criterion\ContentTypeIdentifier::class
                );

                if ($criterion === null) {
                    return false;
                }

                return $criterion->value === $contentTypes;
            },
            'filterByFullText' => function (Query $query, $text): bool {
                $criterion = $this->findCriterionInQueryFilter(
                    $query,
                    Query\Criterion\FullText::class
                );

                if ($criterion === null) {
                    return false;
                }

                return $criterion->value === $text;
            },
            'filterByDateModified' => function (Query $query, $operator, $date): bool {
                $criterion = $this->findCriterionInQueryFilter($query, Query\Criterion\DateMetadata::class);

                if ($criterion === null) {
                    return false;
                }

                if ($criterion->target !== Query\Criterion\DateMetadata::MODIFIED) {
                    return false;
                }

                return $criterion->operator == $operator
                    && $criterion->value[0] == strtotime($date);
            },
            'filterByDateCreated' => function (Query $query, $operator, $date): bool {
                $criterion = $this->findCriterionInQueryFilter($query, Query\Criterion\DateMetadata::class);

                if ($criterion === null) {
                    return false;
                }

                if ($criterion->target !== Query\Criterion\DateMetadata::CREATED) {
                    return false;
                }

                return $criterion->operator == $operator
                    && $criterion->value[0] == strtotime($date);
            },
            'filterByField' => function (Query $query, $field, $operator = null, $value = null): bool {
                $criterion = $this->findCriterionInQueryFilter($query, Query\Criterion\Field::class);

                if ($criterion === null) {
                    return false;
                }

                if ($criterion->target !== $field) {
                    return false;
                }

                if ($operator !== null && $criterion->operator != $operator) {
                    return false;
                }

                return $value === null || $criterion->value == $value;
            },
            'filterByFieldWithOperator' => function (Query $query, $operator): bool {
                $criterion = $this->findCriterionInQueryFilter($query, Query\Criterion\Field::class);
                if ($criterion === null) {
                    return false;
                }

                return $criterion->operator == $operator;
            },
        ];
    }

    private function findCriterionInQueryFilter(Query $query, string $searchedCriterionClass)
    {
        if ($query->filter instanceof Query\Criterion\LogicalOperator) {
            return $this->findCriterionInCriterion($query->filter, $searchedCriterionClass);
        } else {
            if ($query->filter instanceof $searchedCriterionClass) {
                return $query->filter;
            }
        }

        return null;
    }

    private function findCriterionInCriterion(Query\Criterion\LogicalOperator $logicalCriterion, $searchedCriterionClass)
    {
        foreach ($logicalCriterion->criteria as $criterion) {
            if ($criterion instanceof Query\Criterion\LogicalOperator) {
                $criterion = $this->findCriterionInCriterion($criterion, $searchedCriterionClass);
                if ($criterion !== null) {
                    return $criterion;
                }
            }

            if ($criterion instanceof $searchedCriterionClass) {
                return $criterion;
            }
        }

        return null;
    }
}
