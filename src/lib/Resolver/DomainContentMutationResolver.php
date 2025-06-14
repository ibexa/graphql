<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use GraphQL\Error\UserError;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions as RepositoryExceptions;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values as RepositoryValues;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\Exception\UnsupportedFieldTypeException;
use Ibexa\GraphQL\ItemFactory;
use Ibexa\GraphQL\Schema\Domain\Content\NameHelper;
use Ibexa\GraphQL\Value\Item;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserErrors;
use Overblog\GraphQLBundle\Relay\Node\GlobalId;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * @internal
 */
class DomainContentMutationResolver
{
    private Repository $repository;

    /**
     * @var \Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldTypeInputHandler[]
     */
    private array $fieldInputHandlers;

    private NameHelper $nameHelper;

    private ItemFactory $itemFactory;

    public function __construct(
        Repository $repository,
        array $fieldInputHandlers,
        NameHelper $nameHelper,
        ItemFactory $relatedContentItemFactory
    ) {
        $this->repository = $repository;
        $this->fieldInputHandlers = $fieldInputHandlers;
        $this->nameHelper = $nameHelper;
        $this->itemFactory = $relatedContentItemFactory;
    }

    public function updateDomainContent($input, Argument $args, $versionNo, ?string $language): Item
    {
        if (isset($args['id'])) {
            $idArray = GlobalId::fromGlobalId($args['id']);
            $contentId = $idArray['id'];
        } elseif (isset($args['contentId'])) {
            $contentId = $args['contentId'];
        } else {
            throw new UserError('Either id or contentId is required as an argument');
        }

        try {
            $contentInfo = $this->getContentService()->loadContentInfo($contentId);
        } catch (RepositoryExceptions\NotFoundException $e) {
            throw new UserError("Could not load content with ID $contentId");
        } catch (RepositoryExceptions\UnauthorizedException $e) {
            throw new UserError('You are not authorized to load this content');
        }
        try {
            $contentType = $this->getContentTypeService()->loadContentType($contentInfo->contentTypeId);
        } catch (RepositoryExceptions\NotFoundException $e) {
            throw new UserError("Could not load content type with ID $contentInfo->contentTypeId");
        }

        $contentUpdateStruct = $this->getContentService()->newContentUpdateStruct();

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            $inputFieldKey = $this->getInputField($fieldDefinition);
            if (isset($input[$inputFieldKey])) {
                try {
                    $contentUpdateStruct->setField(
                        $fieldDefinition->identifier,
                        $this->getInputFieldValue($input[$inputFieldKey], $fieldDefinition),
                        $language
                    );
                } catch (UnsupportedFieldTypeException $e) {
                    continue;
                }
            }
        }

        if (!isset($versionNo)) {
            try {
                $versionInfo = $this->getContentService()->createContentDraft($contentInfo)->versionInfo;
            } catch (RepositoryExceptions\UnauthorizedException $e) {
                throw new UserError('You are not authorized to create a draft of this Content item');
            }
        } else {
            try {
                $versionInfo = $this->getContentService()->loadVersionInfo($contentInfo, $versionNo);
            } catch (RepositoryExceptions\NotFoundException $e) {
                throw new UserError("Could not find version $versionNo");
            } catch (RepositoryExceptions\UnauthorizedException $e) {
                throw new UserError('You are not authorized to load this version');
            }
            if ($versionInfo->status !== RepositoryValues\Content\VersionInfo::STATUS_DRAFT) {
                try {
                    $versionInfo = $this->getContentService()->createContentDraft($contentInfo, $versionInfo)->versionInfo;
                } catch (RepositoryExceptions\UnauthorizedException $e) {
                    throw new UserError('You are not authorized to create a draft from this version');
                }
            }
        }

        try {
            $contentDraft = $this->getContentService()->updateContent($versionInfo, $contentUpdateStruct);
        } catch (RepositoryExceptions\ContentFieldValidationException $e) {
            throw new UserErrors($this->renderFieldValidationErrors($e, $contentType));
        } catch (RepositoryExceptions\ContentValidationException $e) {
            throw new UserError('The provided input did not validate: ' . $e->getMessage());
        } catch (RepositoryExceptions\UnauthorizedException $e) {
            throw new UserError('You are not authorized to update this version');
        }
        try {
            $this->getContentService()->publishVersion($contentDraft->versionInfo);
        } catch (RepositoryExceptions\BadStateException $e) {
            throw new UserError("The version you're trying to publish is not a draft");
        } catch (RepositoryExceptions\UnauthorizedException $e) {
            throw new UserError('You are not authorized to publish this version');
        }

        return $this->itemFactory->fromContent($this->getContentService()->loadContent($contentDraft->id));
    }

    public function createDomainContent($input, string $contentTypeIdentifier, $parentLocationId, string $language): Item
    {
        try {
            $contentType = $this->getContentTypeService()->loadContentTypeByIdentifier($contentTypeIdentifier);
        } catch (RepositoryExceptions\NotFoundException $e) {
            throw new UserError($e->getMessage(), 0, $e);
        }
        $contentCreateStruct = $this->getContentService()->newContentCreateStruct($contentType, $language);
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            $inputFieldKey = $this->getInputField($fieldDefinition);
            if (isset($input[$inputFieldKey])) {
                $contentCreateStruct->setField(
                    $fieldDefinition->identifier,
                    $this->getInputFieldValue($input[$inputFieldKey], $fieldDefinition),
                    $language
                );
            }
        }

        try {
            $contentDraft = $this->getContentService()->createContent(
                $contentCreateStruct,
                [$this->getLocationService()->newLocationCreateStruct($parentLocationId)]
            );
        } catch (RepositoryExceptions\ContentFieldValidationException $e) {
            throw new UserErrors($this->renderFieldValidationErrors($e, $contentType));
        } catch (\Exception $e) {
            throw new UserError($e->getMessage(), 0, $e);
        }

        try {
            $content = $this->getContentService()->publishVersion($contentDraft->versionInfo);
        } catch (\Exception $e) {
            throw new UserError($e->getMessage(), 0, $e);
        }

        return $this->itemFactory->fromContent($content);
    }

    /**
     * @return array{id: string, contentId: int}
     */
    public function deleteDomainContent(Argument $args): array
    {
        $globalId = null;

        if (isset($args['id'])) {
            $globalId = $args['id'];
            $idArray = GlobalId::fromGlobalId($args['id']);
            $contentId = $idArray['id'];
        } elseif (isset($args['contentId'])) {
            $contentId = $args['contentId'];
        } else {
            throw new UserError('Either id or contentId is required as an argument');
        }

        try {
            $contentInfo = $this->getContentService()->loadContentInfo($contentId);
        } catch (RepositoryExceptions\NotFoundException $e) {
            throw new UserError("Could not find a Content item with ID $contentId");
        } catch (RepositoryExceptions\UnauthorizedException $e) {
            throw new UserError("You are not authorized to load the Content item with ID $contentId");
        }
        if (!isset($globalId)) {
            $globalId = GlobalId::toGlobalId(
                $this->resolveDomainContentType($contentInfo),
                $contentId
            );
        }

        try {
            $this->getContentService()->deleteContent($contentInfo);
        } catch (RepositoryExceptions\UnauthorizedException $e) {
            throw new UserError("You are not authorized to delete the Content item with ID $contentInfo->id");
        }

        return [
            'id' => $globalId,
            'contentId' => $contentId,
        ];
    }

    private function getInputFieldValue($fieldInput, FieldDefinition $fieldDefinition)
    {
        if (isset($this->fieldInputHandlers[$fieldDefinition->fieldTypeIdentifier])) {
            $format = null;
            if (isset($fieldInput['input'])) {
                $input = $fieldInput['input'];
                $format = $fieldInput['format'] ?? null;
            } else {
                $input = $fieldInput;
            }

            return $this->fieldInputHandlers[$fieldDefinition->fieldTypeIdentifier]->toFieldValue($input, $format);
        }
    }

    public function resolveDomainContentType(RepositoryValues\Content\ContentInfo $contentInfo)
    {
        static $contentTypesMap = [], $contentTypesLoadErrors = [];

        if (!isset($contentTypesMap[$contentInfo->contentTypeId])) {
            try {
                $contentTypesMap[$contentInfo->contentTypeId] = $this->getContentTypeService()->loadContentType($contentInfo->contentTypeId);
            } catch (\Exception $e) {
                $contentTypesLoadErrors[$contentInfo->contentTypeId] = $e;
                throw $e;
            }
        }

        return $this->makeDomainContentTypeName($contentTypesMap[$contentInfo->contentTypeId]);
    }

    private function makeDomainContentTypeName(RepositoryValues\ContentType\ContentType $contentType): string
    {
        $converter = new CamelCaseToSnakeCaseNameConverter(null, false);

        return $converter->denormalize($contentType->identifier) . 'Content';
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\ContentService
     */
    private function getContentService(): ContentService
    {
        return $this->repository->getContentService();
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    private function getContentTypeService(): ContentTypeService
    {
        return $this->repository->getContentTypeService();
    }

    private function getLocationService(): LocationService
    {
        return $this->repository->getLocationService();
    }

    /**
     * @return string[]
     */
    private function renderFieldValidationErrors(RepositoryExceptions\ContentFieldValidationException $e, RepositoryValues\ContentType\ContentType $contentType): array
    {
        $errors = [];
        foreach ($e->getFieldErrors() as $fieldDefId => $fieldErrorByLanguage) {
            $fieldDefinition = $contentType->getFieldDefinitions()->filter(
                static function (FieldDefinition $fieldDefinition) use ($fieldDefId): bool {
                    return $fieldDefinition->id === $fieldDefId;
                }
            )->first();

            // use error from first available language
            $fieldErrors = reset($fieldErrorByLanguage);

            if ($fieldErrors === false) {
                continue;
            }
            foreach ($fieldErrors as $fieldError) {
                $errors[] = sprintf(
                    "Field '%s' failed validation: %s",
                    $fieldDefinition->identifier,
                    (string)$fieldError->getTranslatableMessage()
                );
            }
        }

        return $errors;
    }

    /**
     * Returns the GraphQL schema input field for a field definition.
     * Example: text_line -> textLine.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     *
     * @return string
     */
    private function getInputField(FieldDefinition $fieldDefinition): string
    {
        return $this->nameHelper->fieldDefinitionField($fieldDefinition);
    }
}
