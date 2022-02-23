<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Builder\Input;

class Type extends Input
{
    public function __construct($name, $type, array $properties = [])
    {
        parent::__construct($properties);
        $this->name = $name;
        $this->type = $type;
    }

    public $name;

    public $type;

    public $inherits = [];

    public $interfaces = [];

    public $nodeType;
}

class_alias(Type::class, 'EzSystems\EzPlatformGraphQL\Schema\Builder\Input\Type');
