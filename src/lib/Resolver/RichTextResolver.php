<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use DOMDocument;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter as RichTextConverterInterface;

/**
 * @internal
 */
class RichTextResolver
{
    private Converter $richTextConverter;

    private Converter $richTextEditConverter;

    public function __construct(RichTextConverterInterface $richTextConverter, RichTextConverterInterface $richTextEditConverter)
    {
        $this->richTextConverter = $richTextConverter;
        $this->richTextEditConverter = $richTextEditConverter;
    }

    public function xmlToHtml5(DOMDocument $document): string|false
    {
        return $this->richTextConverter->convert($document)->saveHTML();
    }

    public function xmlToHtml5Edit(DOMDocument $document): string|false
    {
        return $this->richTextEditConverter->convert($document)->saveHTML();
    }

    public function xmlToPlainText(DOMDocument $document): string
    {
        $html = $this->richTextConverter->convert($document)->saveHTML();

        return strip_tags($html);
    }
}
