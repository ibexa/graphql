<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Builder\Input;

class Arg extends Input
{
    public function __construct($name, $type, array $properties = [])
    {
        parent::__construct($properties);
        $this->name = $name;
        $this->type = $type;
    }

    public $name;

    public $type;

    public $description;

    public $defaultValue;
}

class_alias(Arg::class, 'EzSystems\EzPlatformGraphQL\Schema\Builder\Input\Arg');
