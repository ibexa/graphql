<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use GraphQL\Error\UserError;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;

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

    public function resolveSectionById($sectionId): ?Section
    {
        try {
            return $this->sectionService->loadSection($sectionId);
        } catch (UnauthorizedException $e) {
            throw new UserError($e->getMessage());
        }
    }
}

class_alias(SectionResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\SectionResolver');
