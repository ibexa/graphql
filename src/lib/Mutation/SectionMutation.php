<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Mutation;

use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\SectionCreateStruct;

class SectionMutation
{
    private SectionService $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function createSection(array $value)
    {
        $sectionCreateStruct = new SectionCreateStruct(
            [
                'identifier' => $value['identifier'],
                'name' => $value['name'],
            ]
        );

        $section = $this->sectionService->createSection($sectionCreateStruct);

        return $this->mapSectionToPayLoad($value, $section);
    }

    public function deleteSection($value)
    {
        if (isset($value['id'])) {
            $section = $this->sectionService->loadSection($value['id']);
        }

        if (isset($value['identifier'])) {
            $section = $this->sectionService->loadSectionByIdentifier($value['identifier']);
        }

        if (isset($section)) {
            $this->sectionService->deleteSection($section);

            return $this->mapSectionToPayLoad($value, $section);
        }

        return null;
    }

    /**
     * @param $value
     * @param $section
     *
     * @return array
     */
    private function mapSectionToPayLoad(array $value, $section): array
    {
        return [
            'clientMutationId' => $value['clientMutationId'],
            'id' => $section->id,
            'identifier' => $section->identifier,
            'name' => $section->name,
        ];
    }
}
