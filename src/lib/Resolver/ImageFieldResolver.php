<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Variation\VariationHandler;
use Ibexa\Core\FieldType\Image\Type;
use Ibexa\Core\FieldType\Image\Value as ImageFieldValue;
use Ibexa\GraphQL\DataLoader\ContentLoader;
use Overblog\GraphQLBundle\Error\UserError;

/**
 * @internal
 */
class ImageFieldResolver
{
    private VariationHandler $variationHandler;

    private ContentService $contentService;

    private Type $fieldType;

    private ContentLoader $contentLoader;

    public function __construct(
        Type $imageFieldType,
        VariationHandler $variationHandler,
        ContentLoader $contentLoader,
        ContentService $contentService
    ) {
        $this->variationHandler = $variationHandler;
        $this->contentService = $contentService;
        $this->fieldType = $imageFieldType;
        $this->contentLoader = $contentLoader;
    }

    /**
     * @param array{identifier: array<string>} $args
     */
    public function resolveImageVariations(ImageFieldValue $fieldValue, array $args)
    {
        if ($this->fieldType->isEmptyValue($fieldValue)) {
            return null;
        }
        list($content, $field) = $this->getImageField($fieldValue);

        $variations = [];
        foreach ($args['identifier'] as $identifier) {
            $variations[] = $this->variationHandler->getVariation($field, $content->versionInfo, $identifier);
        }

        return $variations;
    }

    /**
     * @param array{identifier: string} $args
     */
    public function resolveImageVariation(ImageFieldValue $fieldValue, array $args)
    {
        if ($this->fieldType->isEmptyValue($fieldValue)) {
            return null;
        }

        list($content, $field) = $this->getImageField($fieldValue);
        $versionInfo = $this->contentService->loadVersionInfo($content->contentInfo);

        return $this->variationHandler->getVariation($field, $versionInfo, $args['identifier']);
    }

    /**
     * @return array{\Ibexa\Contracts\Core\Repository\Values\Content\Content, \Ibexa\Contracts\Core\Repository\Values\Content\Field}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function getImageField(ImageFieldValue $fieldValue): array
    {
        list($contentId, $fieldId) = $this->decomposeImageId($fieldValue);

        $content = $this->contentLoader->findSingle(new Criterion\ContentId($contentId));

        $fieldFound = false;
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $field */
        foreach ($content->getFields() as $field) {
            if ($field->id == $fieldId) {
                $fieldFound = true;
                break;
            }
        }

        if (!$fieldFound) {
            throw new UserError("Could not find an image Field with ID $fieldId");
        }

        // check the field's value
        if ($field->value->uri === null) {
            throw new UserError("Image file {$field->value->id} doesn't exist");
        }

        return [$content, $field];
    }

    protected function decomposeImageId(ImageFieldValue $fieldValue): array
    {
        $idArray = explode('-', $fieldValue->imageId);
        if (count($idArray) != 3) {
            throw new UserError("Invalid image ID {$fieldValue->imageId}");
        }

        return $idArray;
    }
}
