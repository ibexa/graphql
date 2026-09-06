<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\GraphQL\EventListener;

use Ibexa\GraphQL\DataLoader\Exception\ArgumentsException;
use Overblog\GraphQLBundle\Event\ErrorFormattingEvent;
use Overblog\GraphQLBundle\Event\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ArgumentsExceptionListener implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::ERROR_FORMATTING => ['onErrorFormatting', 10],
        ];
    }

    public function onErrorFormatting(ErrorFormattingEvent $event): void
    {
        $exception = $event->getError()->getPrevious();

        if (!$exception instanceof ArgumentsException) {
            return;
        }

        $this->logger->debug(
            sprintf('[GraphQL] %s: %s', ArgumentsException::class, $exception->getMessage()),
            ['exception' => $exception]
        );

        $event->stopPropagation();
    }
}
