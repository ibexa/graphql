<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver;

use GraphQL\Error\UserError;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Overblog\GraphQLBundle\Definition\Argument;

/**
 * @internal
 */
class ObjectStateGroupResolver
{
    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    public function resolveObjectStateGroupById(Argument $args): ObjectStateGroup
    {
        try {
            return $this->objectStateService->loadObjectStateGroup($args['id']);
        } catch (NotFoundException $e) {
            throw new UserError("Object state group with ID: {$args['id']} not found.");
        }
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup[]
     */
    public function resolveObjectStateGroups(): array
    {
        return $this->objectStateService->loadObjectStateGroups();
    }
}

class_alias(ObjectStateGroupResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\ObjectStateGroupResolver');
