<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\SectionService;

/**
 * @internal
 */
class SectionResolver
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\SectionService
     */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function resolveSectionById($sectionId)
    {
        return $this->sectionService->loadSection($sectionId);
    }
}

class_alias(SectionResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\SectionResolver');
