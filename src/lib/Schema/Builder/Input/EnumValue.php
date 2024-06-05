<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Builder\Input;

class EnumValue extends Input
{
    public function __construct($name, array $properties = [])
    {
        parent::__construct($properties);
        $this->name = $name;
    }

    public $name;

    public $value;

    public $description;
}
