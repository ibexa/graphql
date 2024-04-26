<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use GraphQL\Error\UserError;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Core\FieldType;
use Ibexa\GraphQL\DataLoader\ContentLoader;
use Ibexa\GraphQL\ItemFactory;
use Ibexa\GraphQL\Value\Field;

final class RelationFieldResolver
{
    /** @var \Ibexa\GraphQL\DataLoader\ContentLoader */
    private $contentLoader;

    /** @var \Ibexa\GraphQL\ItemFactory */
    private $itemFactory;

    public function __construct(ContentLoader $contentLoader, ItemFactory $relatedContentItemFactory)
    {
        $this->contentLoader = $contentLoader;
        $this->itemFactory = $relatedContentItemFactory;
    }

    public function resolveRelationFieldValue(Field $field, $multiple = false)
    {
        $destinationContentIds = $this->getContentIds($field);

        if (empty($destinationContentIds) || array_key_exists(0, $destinationContentIds) && null === $destinationContentIds[0]) {
            return $multiple ? [] : null;
        }

        $contentItems = $this->contentLoader->find(new Query(
            ['filter' => new Query\Criterion\ContentId($destinationContentIds)]
        ));

        if ($multiple) {
            return array_map(
                function ($contentId) use ($contentItems) {
                    return $this->itemFactory->fromContent(
                        $contentItems[array_search($contentId, array_column($contentItems, 'id'))]
                    );
                },
                $destinationContentIds
            );
        }

        return $contentItems[0] ? $this->itemFactory->fromContent($contentItems[0]) : null;
    }

    /**
     * @return array
     *
     * @throws \GraphQL\Error\UserError if the field isn't a Relation or RelationList value
     */
    private function getContentIds(Field $field): array
    {
        if ($field->value instanceof FieldType\RelationList\Value) {
            return $field->value->destinationContentIds;
        } elseif ($field->value instanceof FieldType\Relation\Value) {
            return [$field->value->destinationContentId];
        } else {
            throw new UserError('\$field does not contain a RelationList or Relation Field value');
        }
    }
}

class_alias(RelationFieldResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\RelationFieldResolver');
