<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType\RichText;

use DOMDocument;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldType\RichText\RichTextInputConverter;

class HtmlRichTextConverter implements RichTextInputConverter
{
    private Converter $xhtml5Converter;

    public function __construct(Converter $xhtml5Converter)
    {
        $this->xhtml5Converter = $xhtml5Converter;
    }

    public function convertToXml($text): DOMDocument
    {
        $text = <<<HTML5EDIT
<section xmlns="http://ibexa.co/namespaces/ezpublish5/xhtml5/edit">$text</section>
HTML5EDIT;

        $dom = new DOMDocument();
        $dom->loadXML($text);

        return $this->xhtml5Converter->convert($dom);
    }
}
