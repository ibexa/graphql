<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldTypeInputHandler;
use Ibexa\FieldTypeRichText\FieldType\RichText as RichTextFieldType;
use Ibexa\GraphQL\Exception\UnsupportedFieldInputFormatException;

class RichText implements FieldTypeInputHandler
{
    /**
     * @var \Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldType\RichText\RichTextInputConverter[]
     */
    private array $inputConverters;

    public function __construct(array $inputConverters)
    {
        $this->inputConverters = $inputConverters;
    }

    /**
     * @param array $input
     * @param null $inputFormat
     *
     * @return \Ibexa\FieldTypeRichText\FieldType\RichText\Value
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
