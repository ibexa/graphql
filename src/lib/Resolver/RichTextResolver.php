<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use DOMDocument;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter as RichTextConverterInterface;

/**
 * @internal
 */
class RichTextResolver
{
    /**
     * @var \Ibexa\Contracts\FieldTypeRichText\RichText\Converter
     */
    private $richTextConverter;

    /**
     * @var \Ibexa\Contracts\FieldTypeRichText\RichText\Converter
     */
    private $richTextEditConverter;

    public function __construct(RichTextConverterInterface $richTextConverter, RichTextConverterInterface $richTextEditConverter)
    {
        $this->richTextConverter = $richTextConverter;
        $this->richTextEditConverter = $richTextEditConverter;
    }

    public function xmlToHtml5(DOMDocument $document)
    {
        return $this->richTextConverter->convert($document)->saveHTML();
    }

    public function xmlToHtml5Edit(DOMDocument $document)
    {
        return $this->richTextEditConverter->convert($document)->saveHTML();
    }

    public function xmlToPlainText(DOMDocument $document)
    {
        $html = $this->richTextConverter->convert($document)->saveHTML();

        return strip_tags($html);
    }
}
