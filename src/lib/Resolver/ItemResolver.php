<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use GraphQL\Error\UserError;
use GraphQL\Executor\Promise\Promise;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\GraphQL\DataLoader\ContentLoader;
use Ibexa\GraphQL\DataLoader\LocationLoader;
use Ibexa\GraphQL\InputMapper\QueryMapper;
use Ibexa\GraphQL\ItemFactory;
use Ibexa\GraphQL\Value\Field;
use Ibexa\GraphQL\Value\Item;
use Overblog\GraphQLBundle\Definition\ArgumentInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Overblog\GraphQLBundle\Relay\Connection\Output\Connection;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;
use Overblog\GraphQLBundle\Resolver\TypeResolver;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * @internal
 */
final class ItemResolver implements QueryInterface
{
    private TypeResolver $typeResolver;

    private QueryMapper $queryMapper;

    private ContentLoader $contentLoader;

    private LocationLoader $locationLoader;

    private ItemFactory $itemFactory;

    public function __construct(
        TypeResolver $typeResolver,
        QueryMapper $queryMapper,
        ContentLoader $contentLoader,
        LocationLoader $locationLoader,
        ItemFactory $currentSiteItemFactory
    ) {
        $this->typeResolver = $typeResolver;
        $this->queryMapper = $queryMapper;
        $this->contentLoader = $contentLoader;
        $this->locationLoader = $locationLoader;
        $this->itemFactory = $currentSiteItemFactory;
    }

    /**
     * Resolves a domain content item by id, and checks that it is of the requested type.
     *
     * @param \Overblog\GraphQLBundle\Definition\Argument|array $args
     * @param string|null $contentTypeIdentifier
     *
     * @throws \GraphQL\Error\UserError if the loaded item's type didn't match the requested type
     * @throws \GraphQL\Error\UserError if no argument was provided
     */
    public function resolveItemOfType($args, $contentTypeIdentifier): Item
    {
        $item = $this->resolveItem($args);

        $contentType = $item->getContentInfo()->getContentType();
        if ($contentType->identifier !== $contentTypeIdentifier) {
            throw new UserError("Content {$item->getContentInfo()->id} is not of type '$contentTypeIdentifier'");
        }

        return $item;
    }

    /**
     * Resolves a domain content item by one of its identifiers.
     *
     * @param \Overblog\GraphQLBundle\Definition\Argument|array $args
     *
     * @throws \GraphQL\Error\UserError if $contentTypeIdentifier was specified, and the loaded item's type didn't match it
     * @throws \GraphQL\Error\UserError if no argument was provided
     */
    public function resolveItem($args): Item
    {
        if (isset($args['id'])) {
            $item = $this->itemFactory->fromContent(
                $this->contentLoader->findSingle(new Query\Criterion\ContentId($args['id']))
            );
        } elseif (isset($args['contentId'])) {
            $item = $this->itemFactory->fromContent(
                $this->contentLoader->findSingle(new Query\Criterion\ContentId($args['contentId']))
            );
        } elseif (isset($args['remoteId'])) {
            $item = $this->itemFactory->fromContent(
                $this->contentLoader->findSingle(new Query\Criterion\RemoteId($args['remoteId']))
            );
        } elseif (isset($args['locationId'])) {
            $item = $this->itemFactory->fromLocation(
                $this->locationLoader->findById($args['locationId'])
            );
        } elseif (isset($args['locationRemoteId'])) {
            $item = $this->itemFactory->fromLocation(
                $this->locationLoader->findByRemoteId($args['locationRemoteId'])
            );
        } elseif (isset($args['urlAlias'])) {
            $item = $this->itemFactory->fromLocation(
                $this->locationLoader->findByUrlAlias($args['urlAlias'])
            );
        } else {
            throw new UserError('Missing required argument contentId, remoteId, locationId or locationRemoteId');
        }

        return $item;
    }

    public function resolveItemFieldValue(Item $item, string $fieldDefinitionIdentifier, $args = null): ?Field
    {
        return Field::fromField($item->getContent()->getField($fieldDefinitionIdentifier, $args['language'] ?? null));
    }

    /**
     * @return \GraphQL\Executor\Promise\Promise|\Overblog\GraphQLBundle\Relay\Connection\Output\Connection<\Ibexa\GraphQL\Value\Item>
     */
    public function resolveItemsOfTypeAsConnection(string $contentTypeIdentifier, ArgumentInterface $args): Connection|Promise
    {
        $query = $args['query'] ?: [];
        $query['ContentTypeIdentifier'] = $contentTypeIdentifier;
        $query['sortBy'] = $args['sortBy'];
        $query = $this->queryMapper->mapInputToLocationQuery($query);

        $paginator = new Paginator(function ($offset, $limit) use ($query): array {
            $query->offset = $offset;
            $query->limit = $limit ?? 10;

            return array_map(
                function (Content $content) {
                    return $this->itemFactory->fromContent($content);
                },
                $this->contentLoader->find($query)
            );
        });

        return $paginator->auto(
            $args,
            function () use ($query) {
                return $this->contentLoader->count($query);
            }
        );
    }

    public function resolveItemType(Item $item): string
    {
        $typeName = $this->makeDomainContentTypeName(
            $item->getContentInfo()->getContentType()
        );

        return  ($this->typeResolver->hasSolution($typeName))
            ? $typeName
            : 'UntypedItem';
    }

    private function makeDomainContentTypeName(ContentType $contentType): string
    {
        $converter = new CamelCaseToSnakeCaseNameConverter(null, false);

        return $converter->denormalize($contentType->identifier) . 'Item';
    }
}
