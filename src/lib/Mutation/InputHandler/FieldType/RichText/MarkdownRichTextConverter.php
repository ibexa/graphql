<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType\RichText;

use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldType\RichText\RichTextInputConverter;
use DOMDocument;
use EzSystems\EzPlatformRichText\eZ\RichText as RichTextFieldType;
use Parsedown;

class MarkdownRichTextConverter implements RichTextInputConverter
{
    /**
     * @var RichTextFieldType\Converter
     */
    private $markdownConverter;

    /**
     * @var RichTextFieldType\Converter
     */
    private $xhtml5Converter;

    public function __construct(RichTextFieldType\Converter $xhtml5Converter)
    {
        $this->xhtml5Converter = $xhtml5Converter;
        $this->markdownConverter = new Parsedown();
    }

    public function convertToXml($text): DOMDocument
    {
        $parseDown = new Parsedown();
        $html = $parseDown->text($text);
        $input = <<<HTML5EDIT
<section xmlns="http://ez.no/namespaces/ezpublish5/xhtml5/edit">$html</section>
HTML5EDIT;
        $dom = new DOMDocument();
        $dom->loadXML($input);

        return $this->xhtml5Converter->convert($dom);
    }
}

class_alias(MarkdownRichTextConverter::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Mutation\InputHandler\FieldType\RichText\MarkdownRichTextConverter');
