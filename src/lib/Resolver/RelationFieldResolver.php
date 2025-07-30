<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver;

use GraphQL\Error\UserError;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Core\FieldType;
use Ibexa\GraphQL\DataLoader\ContentLoader;
use Ibexa\GraphQL\ItemFactory;
use Ibexa\GraphQL\Relay\PageAwareConnection;
use Ibexa\GraphQL\Value\Field;
use Ibexa\GraphQL\Value\Item;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;

final class RelationFieldResolver
{
    public const DEFAULT_LIMIT = 25;

    private ContentLoader $contentLoader;

    private ItemFactory $itemFactory;

    private bool $enablePagination;

    public function __construct(
        ContentLoader $contentLoader,
        ItemFactory $relatedContentItemFactory,
        bool $enablePagination
    ) {
        $this->contentLoader = $contentLoader;
        $this->itemFactory = $relatedContentItemFactory;
        $this->enablePagination = $enablePagination;
    }

    public function resolveRelationFieldValue(Field $field, $multiple = false, ?Argument $args = null)
    {
        $destinationContentIds = $this->getContentIds($field);

        if (empty($destinationContentIds) || array_key_exists(0, $destinationContentIds) && null === $destinationContentIds[0]) {
            return $multiple ? [] : null;
        }

        $query = new Query(
            ['filter' => new Query\Criterion\ContentId($destinationContentIds)]
        );

        if ($multiple) {
            if (!$this->enablePagination || $args === null) {
                $contentItems = $this->contentLoader->find($query);

                return array_map(
                    function (int $contentId) use ($contentItems): Item {
                        return $this->itemFactory->fromContent(
                            $contentItems[array_search($contentId, array_column($contentItems, 'id'), true)]
                        );
                    },
                    $destinationContentIds
                );
            }

            $paginator = new Paginator(function ($offset, $limit) use ($query): array {
                $query->offset = $offset;
                $query->limit = $limit ?? self::DEFAULT_LIMIT;
                $contentItems = $this->contentLoader->find($query);

                return array_map(
                    function (Content $content): Item {
                        return $this->itemFactory->fromContent(
                            $content
                        );
                    },
                    $contentItems
                );
            });

            return PageAwareConnection::fromConnection(
                $paginator->auto(
                    $args,
                    function () use ($query): int {
                        return $this->contentLoader->count($query);
                    }
                ),
                $args
            );
        }

        $query->limit = 1;
        $contentItems = $this->contentLoader->find($query);

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
        }

        if ($field->value instanceof FieldType\Relation\Value) {
            return [$field->value->destinationContentId];
        }

        throw new UserError('\$field does not contain a RelationList or Relation Field value');
    }
}

class_alias(RelationFieldResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\RelationFieldResolver');
