<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\GraphQL\Mapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\ImageAsset;

interface ImageAssetMapperStrategyInterface
{
    public function canProcess(ImageAsset\Value $value): bool;

    public function process(ImageAsset\Value $value): Field;
}
