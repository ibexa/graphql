<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Security;

use Exception;
use GraphQL\Language\AST\ArgumentNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\Parser;
use GraphQL\Language\Visitor;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\MVC\Symfony\Security\User\APIUserProviderInterface;
use Ibexa\Core\MVC\Symfony\Security\UserInterface as IbexaUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

final class JWTAuthenticator extends AbstractAuthenticator implements InteractiveAuthenticatorInterface
{
    private string $username;

    private string $password;

    public function __construct(
        private readonly JWTTokenManagerInterface $tokenManager,
        private readonly APIUserProviderInterface $userProvider,
        private readonly PermissionResolver $permissionResolver,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        $payload = json_decode($request->getContent(), true);
        if (!isset($payload['query'])) {
            return false;
        }

        try {
            $credentials = $this->extractCredentials($payload['query']);
        } catch (Exception) {
            return false;
        }

        if (isset($credentials['username'], $credentials['password'])) {
            $this->username = $credentials['username'];
            $this->password = $credentials['password'];

            return true;
        }

        return false;
    }

    public function authenticate(Request $request): Passport
    {
        $passport = new Passport(
            new UserBadge($this->username, [$this->userProvider, 'loadUserByUsername']),
            new PasswordCredentials($this->password)
        );

        $user = $passport->getUser();
        if ($user instanceof IbexaUser) {
            $this->permissionResolver->setCurrentUserReference($user->getAPIUser());
        }

        $passport->setAttribute('token', $this->tokenManager->create($user));

        return $passport;
    }

    /**
     * @throws \JsonException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        if ($user === null) {
            throw new AuthenticationException('No authenticated user found.', 401);
        }

        return new Response(
            json_encode(
                [
                    'token' => $this->tokenManager->create($user),
                    'message' => null,
                ],
                JSON_THROW_ON_ERROR
            )
        );
    }

    /**
     * @throws \JsonException
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
       return new Response(
           json_encode(
               [
                   'token' => null,
                   'message' => $exception->getMessageKey(),
                ],
               JSON_THROW_ON_ERROR
           ),
           Response::HTTP_FORBIDDEN
       );
    }

    public function isInteractive(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     *
     * @throws \Exception
     */
    private function extractCredentials(string $graphqlQuery): array
    {
        $parsed = Parser::parse($graphqlQuery);
        $credentials = [];
        Visitor::visit(
            $parsed,
            [
                NodeKind::ARGUMENT => static function (ArgumentNode $node) use (&$credentials): void {
                    /** @var \GraphQL\Language\AST\StringValueNode $nodeValue */
                    $nodeValue = $node->value;

                    $credentials[$node->name->value] = $nodeValue->value;
                },
            ]
        );

        return $credentials;
    }
}
