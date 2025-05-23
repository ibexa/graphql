<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Schema\Domain\Content;

use Generator;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Domain\Iterator;

class LanguagesIterator implements Iterator
{
    private LanguageService $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function init(Builder $schema): void
    {
    }

    public function iterate(): Generator
    {
        foreach ($this->languageService->loadLanguages() as $language) {
            yield ['Language' => $language];
        }
    }
}
