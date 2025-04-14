<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use GraphQL\Error\UserError;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\FieldType;
use Ibexa\GraphQL\DataLoader\ContentLoader;
use Ibexa\GraphQL\DataLoader\ContentTypeLoader;
use Ibexa\GraphQL\InputMapper\QueryMapper;
use Ibexa\GraphQL\Value\Field;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Overblog\GraphQLBundle\Resolver\TypeResolver;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * @internal
 */
class DomainContentResolver implements QueryInterface
{
    private TypeResolver $typeResolver;

    private QueryMapper $queryMapper;

    private Repository $repository;

    private ContentLoader $contentLoader;

    private ContentTypeLoader $contentTypeLoader;

    public function __construct(
        Repository $repository,
        TypeResolver $typeResolver,
        QueryMapper $queryMapper,
        ContentLoader $contentLoader,
        ContentTypeLoader $contentTypeLoader
    ) {
        $this->repository = $repository;
        $this->typeResolver = $typeResolver;
        $this->queryMapper = $queryMapper;
        $this->contentLoader = $contentLoader;
        $this->contentTypeLoader = $contentTypeLoader;
    }

    /**
     * @param string $contentTypeIdentifier
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content[]
     */
    public function resolveDomainContentItems($contentTypeIdentifier, Argument $query): array
    {
        return $this->findContentItemsByTypeIdentifier($contentTypeIdentifier, $query);
    }

    /**
     * Resolves a domain content item by id, and checks that it is of the requested type.
     *
     * @param \Overblog\GraphQLBundle\Definition\Argument|array $args
     * @param string|null $contentTypeIdentifier
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     *
     * @throws \GraphQL\Error\UserError if $contentTypeIdentifier was specified, and the loaded item's type didn't match it
     * @throws \GraphQL\Error\UserError if no argument was provided
     */
    public function resolveDomainContentItem($args, $contentTypeIdentifier)
    {
        if (isset($args['id'])) {
            $criterion = new Query\Criterion\ContentId($args['id']);
        } elseif (isset($args['remoteId'])) {
            $criterion = new Query\Criterion\RemoteId($args['remoteId']);
        } elseif (isset($args['locationId'])) {
            $criterion = new Query\Criterion\LocationId($args['locationId']);
        } else {
            throw new UserError('Missing required argument id, remoteId or locationId');
        }

        $content = $this->contentLoader->findSingle($criterion);

        $contentType = $this->contentTypeLoader->load($content->contentInfo->contentTypeId);

        if (null !== $contentTypeIdentifier && $contentType->identifier !== $contentTypeIdentifier) {
            throw new UserError("Content {$content->contentInfo->id} is not of type '$contentTypeIdentifier'");
        }

        return $content;
    }

    /**
     * @param string $contentTypeIdentifier
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content[]
     */
    private function findContentItemsByTypeIdentifier($contentTypeIdentifier, Argument $args): array
    {
        $input = $args['query'];
        $input['ContentTypeIdentifier'] = $contentTypeIdentifier;
        if (isset($args['sortBy'])) {
            $input['sortBy'] = $args['sortBy'];
        }

        return $this->contentLoader->find(
            $this->queryMapper->mapInputToQuery($input)
        );
    }

    public function resolveMainUrlAlias(Content $content)
    {
        $aliases = $this->repository->getURLAliasService()->listLocationAliases(
            $this->getLocationService()->loadLocation($content->contentInfo->mainLocationId),
            false
        );

        return isset($aliases[0]->path) ? $aliases[0]->path : null;
    }

    public function resolveDomainRelationFieldValue(?Field $field, $multiple = false)
    {
        if ($field === null) {
            return null;
        }

        $destinationContentIds = $this->getContentIds($field);

        if (empty($destinationContentIds) || array_key_exists(0, $destinationContentIds) && null === $destinationContentIds[0]) {
            return $multiple ? [] : null;
        }

        $contentItems = $this->contentLoader->find(new Query(
            ['filter' => new Query\Criterion\ContentId($destinationContentIds)]
        ));

        if ($multiple) {
            return array_map(
                static function ($contentId) use ($contentItems) {
                    return $contentItems[array_search($contentId, array_column($contentItems, 'id'))];
                },
                $destinationContentIds
            );
        }

        return $contentItems[0] ?? null;
    }

    public function resolveDomainContentType(Content $content)
    {
        $typeName = $this->makeDomainContentTypeName(
            $this->contentTypeLoader->load($content->contentInfo->contentTypeId)
        );

        return  ($this->typeResolver->hasSolution($typeName))
            ? $typeName
            : 'UntypedContent';
    }

    private function makeDomainContentTypeName(ContentType $contentType): string
    {
        $converter = new CamelCaseToSnakeCaseNameConverter(null, false);

        return $converter->denormalize($contentType->identifier) . 'Content';
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\LocationService
     */
    private function getLocationService(): LocationService
    {
        return $this->repository->getLocationService();
    }

    /**
     * @return array
     *
     * @throws \GraphQL\Error\UserError if the field isn't a Relation or RelationList value
     */
    private function getContentIds(Field $field)
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
