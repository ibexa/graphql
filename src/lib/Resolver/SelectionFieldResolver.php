<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\GraphQL\DataLoader\ContentTypeLoader;
use Ibexa\GraphQL\Value\Field;

/**
 * @internal
 */
class SelectionFieldResolver
{
    private ContentTypeLoader $contentTypeLoader;

    public function __construct(
        ContentTypeLoader $contentTypeLoader
    ) {
        $this->contentTypeLoader = $contentTypeLoader;
    }

    public function resolveSelectionFieldValue(?Field $field, Content $content)
    {
        if ($field === null || empty($field->value->selection)) {
            return null;
        }

        $fieldDefinition = $this
            ->contentTypeLoader->load($content->contentInfo->contentTypeId)
            ->getFieldDefinition($field->fieldDefIdentifier);

        $options = $this->getOptions($content, $field, $fieldDefinition);

        if ($fieldDefinition->getFieldSettings()['isMultiple']) {
            $return = [];
            foreach ($field->value->selection as $selectedItemId) {
                $return[] = $options[$selectedItemId];
            }
        } else {
            reset($field->value->selection);
            $return = $options[current($field->value->selection)];
        }

        return $return;
    }

    /**
     * Returns the options set based on the language.
     *
     * @return array
     */
    private function getOptions(Content $content, Field $field, FieldDefinition $fieldDefinition)
    {
        $fieldSettings = $fieldDefinition->getFieldSettings();

        if (isset($fieldSettings['multilingualOptions'])) {
            $multilingualOptions = $fieldSettings['multilingualOptions'];
            $mainLanguageCode = $content->contentInfo->mainLanguageCode;

            if (isset($multilingualOptions[$field->languageCode])) {
                return $multilingualOptions[$field->languageCode];
            } elseif (isset($multilingualOptions[$mainLanguageCode])) {
                return $multilingualOptions[$mainLanguageCode];
            }
        }

        return $fieldSettings['options'];
    }
}
