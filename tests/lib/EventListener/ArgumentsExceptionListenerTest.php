<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\GraphQL\EventListener;

use GraphQL\Error\Error;
use Ibexa\GraphQL\DataLoader\Exception\ArgumentsException;
use Ibexa\GraphQL\EventListener\ArgumentsExceptionListener;
use Overblog\GraphQLBundle\Event\ErrorFormattingEvent;
use Overblog\GraphQLBundle\Event\Events;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class ArgumentsExceptionListenerTest extends TestCase
{
    /** @var \Psr\Log\LoggerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private LoggerInterface $logger;

    private ArgumentsExceptionListener $listener;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->listener = new ArgumentsExceptionListener($this->logger);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [Events::ERROR_FORMATTING => ['onErrorFormatting', 10]],
            ArgumentsExceptionListener::getSubscribedEvents(),
        );
    }

    public function testOnErrorFormattingLogsAndStopsPropagationForArgumentsException(): void
    {
        $exception = new ArgumentsException('invalid arguments');
        $event = new ErrorFormattingEvent(new Error('Internal server error', null, null, [], null, $exception), []);

        $this->logger
            ->expects(self::once())
            ->method('debug')
            ->with(
                sprintf('[GraphQL] %s: %s', ArgumentsException::class, 'invalid arguments'),
                ['exception' => $exception],
            );

        $this->listener->onErrorFormatting($event);

        self::assertTrue($event->isPropagationStopped());
    }

    public function testOnErrorFormattingIgnoresExceptionsThatAreNotArgumentsExceptions(): void
    {
        $exception = new RuntimeException('some other error');
        $event = new ErrorFormattingEvent(new Error('Internal server error', null, null, [], null, $exception), []);

        $this->logger
            ->expects(self::never())
            ->method('debug');

        $this->listener->onErrorFormatting($event);

        self::assertFalse($event->isPropagationStopped());
    }
}
