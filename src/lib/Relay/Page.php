<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Relay;

/**
 * A Page in a PageAwareConnection.
 */
class Page
{
    /** @var int */
    public $number;

    /** @var string */
    public $cursor;

    public function __construct(int $number, string $cursor)
    {
        $this->number = $number;
        $this->cursor = $cursor;
    }
}

class_alias(Page::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Relay\Page');
