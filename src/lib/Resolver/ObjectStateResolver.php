<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use GraphQL\Error\UserError;
use Overblog\GraphQLBundle\Definition\Argument;

/**
 * @internal
 */
class ObjectStateResolver
{
    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    public function resolveObjectStateById(Argument $args): ObjectState
    {
        try {
            return $this->objectStateService->loadObjectState($args['id']);
        } catch (NotFoundException $e) {
            throw new UserError("Object state with ID: {$args['id']} not found.");
        }
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState[]
     */
    public function resolveObjectStatesByGroup(ObjectStateGroup $objectStateGroup): array
    {
        return $this->objectStateService->loadObjectStates($objectStateGroup);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState[]
     */
    public function resolveObjectStatesByGroupId(Argument $args): array
    {
        try {
            $group = $this->objectStateService->loadObjectStateGroup($args['groupId']);
        } catch (NotFoundException $e) {
            throw new UserError("Object state group with ID: {$args['groupId']} not found.");
        }

        return $this->objectStateService->loadObjectStates($group);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState[]
     */
    public function resolveObjectStateByContentInfo(ContentInfo $contentInfo): array
    {
        $objectStates = [];
        foreach ($this->objectStateService->loadObjectStateGroups() as $group) {
            $objectStates[] = $this->objectStateService->getContentState($contentInfo, $group);
        }

        return $objectStates;
    }
}

class_alias(ObjectStateResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\ObjectStateResolver');
