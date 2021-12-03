<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldType\RichText;

interface RichTextInputConverter
{
    public function convertToXml($text): \DOMDocument;
}

class_alias(RichTextInputConverter::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Mutation\InputHandler\FieldType\RichText\RichTextInputConverter');
