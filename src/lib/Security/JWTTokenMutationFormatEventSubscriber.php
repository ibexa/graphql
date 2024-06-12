<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\GraphQL\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Needed only to properly adjust authorization (either successful or failed) response to meet former JWT Token Mutation format.
 */
final readonly class JWTTokenMutationFormatEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => ['onAuthorizationFinishes', 10],
            LoginFailureEvent::class => ['onAuthorizationFinishes', 10],
        ];
    }

    /**
     * @throws \JsonException
     */
    public function onAuthorizationFinishes(LoginSuccessEvent|LoginFailureEvent $event): void
    {
        $response = $event->getResponse();
        $response->setContent(
            $this->formatMutationResponseData($response->getContent())
        );
    }

    /**
     * @throws \JsonException
     */
    private function formatMutationResponseData(mixed $data): string
    {
        return json_encode([
            'data' => [
                'CreateToken' => json_decode($data, true, 512, JSON_THROW_ON_ERROR),
            ],
        ]);
    }
}
