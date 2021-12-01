<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType\RichText;

use DOMDocument;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldType\RichText\RichTextInputConverter;
use Ibexa\FieldTypeRichText\eZ\RichText as RichTextFieldType;

class HtmlRichTextConverter implements RichTextInputConverter
{
    /**
     * @var RichTextFieldType\Converter
     */
    private $xhtml5Converter;

    public function __construct(RichTextFieldType\Converter $xhtml5Converter)
    {
        $this->xhtml5Converter = $xhtml5Converter;
    }

    public function convertToXml($text): DOMDocument
    {
        $text = <<<HTML5EDIT
<section xmlns="http://ez.no/namespaces/ezpublish5/xhtml5/edit">$text</section>
HTML5EDIT;

        $dom = new DOMDocument();
        $dom->loadXML($text);

        return $this->xhtml5Converter->convert($dom);
    }
}

class_alias(HtmlRichTextConverter::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Mutation\InputHandler\FieldType\RichText\HtmlRichTextConverter');
