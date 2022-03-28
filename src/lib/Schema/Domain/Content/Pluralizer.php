<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Schema\Domain\Content;

/**
 * @internal
 */
final class Pluralizer
{
    public function pluralize(string $name): string
    {
        if (substr($name, -1) === 'f') {
            return substr($name, 0, -1) . 'ves';
        }

        if (substr($name, -1) === 'fe') {
            return substr($name, 0, -2) . 'ves';
        }

        if (substr($name, -1) === 'y') {
            if (\in_array(substr($name, -2, 1), ['a', 'e', 'i', 'o', 'u'])) {
                return $name . 's';
            } else {
                return substr($name, 0, -1) . 'ies';
            }
        }

        if (substr($name, -2) === 'is') {
            return substr($name, 0, -2) . 'es';
        }

        if (substr($name, -2) === 'us') {
            return substr($name, 0, -2) . 'i';
        }

        if (\in_array(substr($name, -2), ['on', 'um'])) {
            return substr($name, 0, -2) . 'a';
        }

        if (substr($name, -2) === 'is') {
            return substr($name, 0, -2) . 'es';
        }

        if (
            preg_match('/(s|sh|ch|x|z)$/', $name) ||
            substr($name, -1) === 'o'
        ) {
            return $name . 'es';
        }

        return $name . 's';
    }
}
