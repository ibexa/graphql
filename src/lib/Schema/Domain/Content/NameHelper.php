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

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemConnectionField() instead.
     */
    public function domainContentCollectionField(ContentType $contentType)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemConnectionFieldName() instead.', E_USER_DEPRECATED);

        return $this->itemConnectionField($contentType);
    }

    public function itemConnectionField(ContentType $contentType)
    {
        return $this->pluralize(lcfirst($this->toCamelCase($contentType->identifier)));
    }

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemName() instead.
     */
    public function domainContentName(ContentType $contentType)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemName() instead.', E_USER_DEPRECATED);

        return $this->itemName($contentType);
    }

    public function itemName(ContentType $contentType)
    {
        return ucfirst($this->toCamelCase($contentType->identifier)) . 'Item';
    }

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemConnectionName() instead.
     */
    public function domainContentConnection($contentType)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemConnectionName() instead.', E_USER_DEPRECATED);

        return $this->itemConnectionName($contentType);
    }

    public function itemConnectionName($contentType)
    {
        return ucfirst($this->toCamelCase($contentType->identifier)) . 'ItemConnection';
    }

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemCreateInputName() instead.
     */
    public function domainContentCreateInputName(ContentType $contentType)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemCreateInputName() instead.', E_USER_DEPRECATED);

        return $this->itemCreateInputName($contentType);
    }

    public function itemCreateInputName(ContentType $contentType)
    {
        return ucfirst($this->toCamelCase($contentType->identifier)) . 'ItemCreateInput';
    }

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemUpdateInputName() instead.
     */
    public function domainContentUpdateInputName(ContentType $contentType)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemUpdateInputName() instead.', E_USER_DEPRECATED);

        return $this->itemUpdateInputName($contentType);
    }

    public function itemUpdateInputName(ContentType $contentType)
    {
        return ucfirst($this->toCamelCase($contentType->identifier)) . 'ItemUpdateInput';
    }

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemUpdateInputName() instead.
     */
    public function domainContentTypeName(ContentType $contentType)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemTypeName() instead.', E_USER_DEPRECATED);

        return $this->itemTypeName($contentType);
    }

    public function itemTypeName(ContentType $contentType)
    {
        return ucfirst($this->toCamelCase($contentType->identifier)) . 'ItemType';
    }

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemField() instead.
     */
    public function domainContentField(ContentType $contentType)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemField() instead.', E_USER_DEPRECATED);

        return $this->itemField($contentType);
    }

    public function itemField(ContentType $contentType)
    {
        return lcfirst($this->toCamelCase($contentType->identifier));
    }

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemMutationCreateItemField() instead.
     */
    public function domainMutationCreateContentField(ContentType $contentType)
    {
        return $this->itemMutationCreateItemField($contentType);
    }

    public function itemMutationCreateItemField(ContentType $contentType)
    {
        return 'create' . ucfirst($this->itemField($contentType));
    }

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemMutationUpdateItemField() instead.
     */
    public function domainMutationUpdateContentField(ContentType $contentType)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemMutationUpdateItemField() instead.', E_USER_DEPRECATED);

        return $this->itemMutationUpdateItemField($contentType);
    }

    public function itemMutationUpdateItemField($contentType)
    {
        return 'update' . ucfirst($this->itemField($contentType));
    }

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemGroupName() instead.
     */
    public function domainGroupName(ContentTypeGroup $contentTypeGroup)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemGroupName() instead.', E_USER_DEPRECATED);

        return $this->itemGroupName($contentTypeGroup);
    }

    public function itemGroupName(ContentTypeGroup $contentTypeGroup)
    {
        return 'ItemGroup' . ucfirst($this->toCamelCase($this->sanitizeContentTypeGroupIdentifier($contentTypeGroup)));
    }

    /**
     * @deprecated since v3.0, will be removed in v4.0. Use itemGroupTypesName() instead.
     */
    public function domainGroupTypesName(ContentTypeGroup $contentTypeGroup)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemGroupTypesName() instead.', E_USER_DEPRECATED);

        return $this->itemGroupTypesName($contentTypeGroup);
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

    public function domainGroupField(ContentTypeGroup $contentTypeGroup)
    {
        @trigger_error('Deprecated since v3.0, will be removed in v4.0. Use itemGroupField() instead.', E_USER_DEPRECATED);

        return $this->itemGroupField($contentTypeGroup);
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

class_alias(NameHelper::class, 'EzSystems\EzPlatformGraphQL\Schema\Domain\Content\NameHelper');
