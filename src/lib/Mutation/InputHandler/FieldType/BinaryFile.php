<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Mutation\InputHandler\FieldType;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\GraphQL\Mutation\InputHandler\FieldTypeInputHandler;
use Ibexa\Core\FieldType\BinaryFile as BInaryFileFieldType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BinaryFile implements FieldTypeInputHandler
{
    public function toFieldValue($input, $inputFormat = null): Value
    {
        if (!$input['file'] instanceof UploadedFile) {
            return null;
        }

        $file = $input['file'];

        return new BinaryFileFieldType\Value([
            'fileName' => $file->getClientOriginalName(),
            'inputUri' => $file->getPathname(),
            'fileSize' => $file->getSize(),
        ]);
    }
}

class_alias(BinaryFile::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Mutation\InputHandler\FieldType\BinaryFile');
