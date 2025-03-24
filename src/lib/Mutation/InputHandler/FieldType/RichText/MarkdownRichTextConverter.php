<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType\RichText;

use DOMDocument;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldType\RichText\RichTextInputConverter;
use Parsedown;

class MarkdownRichTextConverter implements RichTextInputConverter
{
    /** @var \Ibexa\Contracts\FieldTypeRichText\RichText\Converter */
    private Parsedown $markdownConverter;

    private Converter $xhtml5Converter;

    public function __construct(Converter $xhtml5Converter)
    {
        $this->xhtml5Converter = $xhtml5Converter;
        $this->markdownConverter = new Parsedown();
    }

    public function convertToXml($text): DOMDocument
    {
        $parseDown = new Parsedown();
        $html = $parseDown->text($text);
        $input = <<<HTML5EDIT
<section xmlns="http://ibexa.co/namespaces/ezpublish5/xhtml5/edit">$html</section>
HTML5EDIT;
        $dom = new DOMDocument();
        $dom->loadXML($input);

        return $this->xhtml5Converter->convert($dom);
    }
}
