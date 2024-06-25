<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Mutation;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Core\MVC\Symfony\Security\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument;

final readonly class AuthenticationMutation
{
    public function __construct(
        private JWTTokenManagerInterface $tokenManager,
        private UserService $userService
    ) {
    }

    /**
     * @return array<string, ?string>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function createToken(Argument $args): array
    {
        if (!isset($args['username'], $args['password'])) {
            return [
                'message' => 'Missing username or password',
                'token' => null,
            ];
        }

        try {
            $user = $this->userService->loadUserByLogin($args['username']);
            $this->userService->checkUserCredentials($user, $args['password']);
        } catch (NotFoundException) {
            return [
                'message' => 'Wrong username or password',
                'token' => null,
            ];
        }

        return [
            'token' => $this->tokenManager->create(new User($user)),
        ];
    }
}
