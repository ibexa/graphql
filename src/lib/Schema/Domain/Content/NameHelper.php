<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Schema\Domain\Content;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\Schema\Domain\BaseNameHelper;
use Ibexa\GraphQL\Schema\Domain\Pluralizer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class NameHelper extends BaseNameHelper implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string[]
     */
    private array $fieldNameOverrides;

    public function __construct(
        array $fieldNameOverrides,
        Pluralizer $pluralizer,
        LoggerInterface $logger = null
    ) {
        parent::__construct($pluralizer);

        $this->logger = $logger ?? new NullLogger();
        $this->fieldNameOverrides = $fieldNameOverrides;
    }

    public function itemConnectionField(ContentType $contentType)
    {
        return $this->pluralize(lcfirst($this->toCamelCase($contentType->identifier)));
    }

    public function itemName(ContentType $contentType)
    {
        return ucfirst($this->toCamelCase($contentType->identifier)) . 'Item';
    }

    public function itemConnectionName($contentType)
    {
        return ucfirst($this->toCamelCase($contentType->identifier)) . 'ItemConnection';
    }

    public function itemCreateInputName(ContentType $contentType)
    {
        return ucfirst($this->toCamelCase($contentType->identifier)) . 'ItemCreateInput';
    }

    public function itemUpdateInputName(ContentType $contentType)
    {
        return ucfirst($this->toCamelCase($contentType->identifier)) . 'ItemUpdateInput';
    }

    public function itemTypeName(ContentType $contentType)
    {
        return ucfirst($this->toCamelCase($contentType->identifier)) . 'ItemType';
    }

    public function itemField(ContentType $contentType)
    {
        return lcfirst($this->toCamelCase($contentType->identifier));
    }

    public function itemMutationCreateItemField(ContentType $contentType)
    {
        return 'create' . ucfirst($this->itemField($contentType));
    }

    public function itemMutationUpdateItemField($contentType)
    {
        return 'update' . ucfirst($this->itemField($contentType));
    }

    public function itemGroupName(ContentTypeGroup $contentTypeGroup)
    {
        return 'ItemGroup' . ucfirst($this->toCamelCase($this->sanitizeContentTypeGroupIdentifier($contentTypeGroup)));
    }

    public function itemGroupTypesName(ContentTypeGroup $contentTypeGroup)
    {
        return sprintf(
            'ItemGroup%sTypes',
            ucfirst($this->toCamelCase(
                $this->sanitizeContentTypeGroupIdentifier($contentTypeGroup)
            ))
        );
    }

    public function itemGroupField(ContentTypeGroup $contentTypeGroup)
    {
        return lcfirst($this->toCamelCase($this->sanitizeContentTypeGroupIdentifier($contentTypeGroup)));
    }

    public function fieldDefinitionField(FieldDefinition $fieldDefinition)
    {
        $fieldName = lcfirst($this->toCamelCase($fieldDefinition->identifier));

        // Workaround for https://issues.ibexa.co/browse/EZP-32261
        if (\array_key_exists($fieldName, $this->fieldNameOverrides)) {
            $newFieldName = $this->fieldNameOverrides[$fieldName];
            $this->logger->warning(
                sprintf(
                    'The field name "%s" was overridden to "%s"',
                    $fieldName,
                    $newFieldName
                )
            );

            return $newFieldName;
        }

        return $fieldName;
    }

    /**
     * Removes potential spaces in content types groups names.
     * (content types groups identifiers are actually their name).
     */
    protected function sanitizeContentTypeGroupIdentifier(ContentTypeGroup $contentTypeGroup): string
    {
        return preg_replace('/[^A-Za-z0-9_]/', '_', $contentTypeGroup->identifier);
    }
}
