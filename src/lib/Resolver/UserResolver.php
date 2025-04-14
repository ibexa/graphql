<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\Resolver;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Overblog\GraphQLBundle\Error\UserWarning;

/**
 * @internal
 */
class UserResolver
{
    private UserService $userService;

    private LocationService $locationService;

    public function __construct(UserService $userService, LocationService $locationService)
    {
        $this->userService = $userService;
        $this->locationService = $locationService;
    }

    /**
     * @param array{id?: int, email?: string, login?: string} $args
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User|iterable<\Ibexa\Contracts\Core\Repository\Values\User\User>
     */
    public function resolveUser($args): User|iterable
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

        throw new UserWarning('Missing: id, email or login argument');
    }

    public function resolveUserById(int $userId): ?User
    {
        try {
            return $this->userService->loadUser($userId);
        } catch (NotFoundException $e) {
            throw new UserWarning($e->getMessage());
        }
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserGroup[]
     */
    public function resolveUserGroupsByUserId(int $userId): iterable
    {
        return $this->userService->loadUserGroupsOfUser(
            $this->userService->loadUser($userId)
        );
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\User\User>
     */
    public function resolveUsersOfGroup(UserGroup $userGroup): iterable
    {
        return $this->userService->loadUsersOfUserGroup(
            $userGroup
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserGroup
     */
    public function resolveUserGroupById(int $userGroupId): UserGroup
    {
        return $this->userService->loadUserGroup($userGroupId);
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\User\UserGroup>
     */
    public function resolveUserGroupSubGroups(UserGroup $userGroup): iterable
    {
        return $this->userService->loadSubUserGroups($userGroup);
    }


    /**
     * @param array{id: int} $args
     *
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\User\UserGroup>
     */
    public function resolveUserGroups(array $args): iterable
    {
        return $this->userService->loadSubUserGroups(
            $this->userService->loadUserGroup(
                $this->locationService->loadLocation($args['id'])->contentId
            )
        );
    }

    /**
     * @param array{identifier?: string} $args
     *
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Field|null>
     */
    public function resolveContentFields(Content $content, array $args): iterable
    {
        if (isset($args['identifier'])) {
            return [$content->getField($args['identifier'])];
        }

        return $content->getFieldsByLanguage();
    }
}
