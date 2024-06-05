<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldTypeInputHandler;
use Ibexa\Core\FieldType\Media as MediaFieldType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Media implements FieldTypeInputHandler
{
    public function toFieldValue($input, $inputFormat = null): Value
    {
        if (!$input['file'] instanceof UploadedFile) {
            return null;
        }

        $file = $input['file'];

        $value = new MediaFieldType\Value([
            'fileName' => $input['fileName'] ?? $file->getClientOriginalName(),
            'inputUri' => $file->getPathname(),
            'fileSize' => $file->getSize(),
        ]);

        foreach (['hasController', 'loop', 'autoplay', 'width', 'height'] as $property) {
            if (isset($input[$property])) {
                $value->$property = $input[$property];
            }
        }

        return $value;
    }
}
