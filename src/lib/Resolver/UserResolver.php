<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;

/**
 * @internal
 */
class UserResolver
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\UserService
     */
    private $userService;

    /**
     * @var \Ibexa\Contracts\Core\Repository\LocationService
     */
    private $locationService;

    public function __construct(UserService $userService, LocationService $locationService)
    {
        $this->userService = $userService;
        $this->locationService = $locationService;
    }

    public function resolveUser($args)
    {
        if (isset($args['id'])) {
            return $this->userService->loadUser($args['id']);
        }

        if (isset($args['email'])) {
            return $this->userService->loadUsersByEmail($args['email']);
        }

        if (isset($args['login'])) {
            return $this->userService->loadUserByLogin($args['login']);
        }
    }

    public function resolveUserById($userId)
    {
        return $this->userService->loadUser($userId);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserGroup[]
     */
    public function resolveUserGroupsByUserId($userId)
    {
        return $this->userService->loadUserGroupsOfUser(
            $this->userService->loadUser($userId)
        );
    }

    public function resolveUsersOfGroup(UserGroup $userGroup)
    {
        return $this->userService->loadUsersOfUserGroup(
            $userGroup
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserGroup
     */
    public function resolveUserGroupById($userGroupId)
    {
        return $this->userService->loadUserGroup($userGroupId);
    }

    public function resolveUserGroupSubGroups(UserGroup $userGroup)
    {
        return $this->userService->loadSubUserGroups($userGroup);
    }

    public function resolveUserGroups($args)
    {
        return $this->userService->loadSubUserGroups(
            $this->userService->loadUserGroup(
                $this->locationService->loadLocation($args['id'])->contentId
            )
        );
    }

    public function resolveContentFields(Content $content, $args)
    {
        if (isset($args['identifier'])) {
            return [$content->getField($args['identifier'])];
        }

        return $content->getFieldsByLanguage();
    }
}

class_alias(UserResolver::class, 'EzSystems\EzPlatformGraphQL\GraphQL\Resolver\UserResolver');
