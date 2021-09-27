<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType;

use eZ\Publish\SPI\FieldType\Value;
use Ibexa\GraphQL\Exception\UnsupportedFieldInputFormatException;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldType\RichText\RichTextInputConverter;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldTypeInputHandler;
use EzSystems\EzPlatformRichText\eZ\FieldType\RichText as RichTextFieldType;

class RichText implements FieldTypeInputHandler
{
    /**
     * @var RichTextInputConverter[]
     */
    private $inputConverters;

    public function __construct(array $inputConverters)
    {
        $this->inputConverters = $inputConverters;
    }

    /**
     * @param array $input
     * @param null $inputFormat
     *
     * @return \EzSystems\EzPlatformRichText\eZ\FieldType\RichText\Value
     */
    public function toFieldValue($input, $inputFormat = null): Value
    {
        if (isset($this->inputConverters[$inputFormat])) {
            $fieldValue = new RichTextFieldType\Value(
                $this->inputConverters[$inputFormat]->convertToXml($input)
            );
        } else {
            throw new UnsupportedFieldInputFormatException('ezrichtext', $inputFormat);
        }

        return $fieldValue;
    }
}

class_alias(RichText::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Mutation\InputHandler\FieldType\RichText');
