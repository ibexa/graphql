<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Schema\Domain;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

abstract class BaseNameHelper
{
    private CamelCaseToSnakeCaseNameConverter $caseConverter;

    private Pluralizer $pluralizer;

    public function __construct(Pluralizer $pluralizer)
    {
        $this->caseConverter = new CamelCaseToSnakeCaseNameConverter(null, false);
        $this->pluralizer = $pluralizer;
    }

    protected function toCamelCase(string $string): string
    {
        return $this->caseConverter->denormalize($string);
    }

    protected function pluralize(string $name): string
    {
        return $this->pluralizer->pluralize($name);
    }
}
