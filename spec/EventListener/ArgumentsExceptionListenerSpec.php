<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace spec\Ibexa\GraphQL\EventListener;

use GraphQL\Error\Error;
use Ibexa\GraphQL\DataLoader\Exception\ArgumentsException;
use Ibexa\GraphQL\EventListener\ArgumentsExceptionListener;
use Overblog\GraphQLBundle\Event\ErrorFormattingEvent;
use Overblog\GraphQLBundle\Event\Events;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ArgumentsExceptionListenerSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ArgumentsExceptionListener::class);
    }

    function it_subscribes_to_the_error_formatting_event()
    {
        $expected = [Events::ERROR_FORMATTING => ['onErrorFormatting', 10]];

        if (ArgumentsExceptionListener::getSubscribedEvents() !== $expected) {
            throw new RuntimeException('Unexpected subscribed events.');
        }
    }

    function it_logs_and_stops_propagation_for_an_arguments_exception(LoggerInterface $logger)
    {
        $exception = new ArgumentsException('invalid arguments');
        $event = new ErrorFormattingEvent(new Error('Internal server error', null, null, null, null, $exception), []);

        $logger->debug(
            sprintf('[GraphQL] %s: %s', ArgumentsException::class, 'invalid arguments'),
            ['exception' => $exception]
        )->shouldBeCalled();

        $this->onErrorFormatting($event);

        if (!$event->isPropagationStopped()) {
            throw new RuntimeException('Expected event propagation to be stopped.');
        }
    }

    function it_ignores_exceptions_that_are_not_arguments_exceptions(LoggerInterface $logger)
    {
        $exception = new RuntimeException('some other error');
        $event = new ErrorFormattingEvent(new Error('Internal server error', null, null, null, null, $exception), []);

        $logger->debug(Argument::cetera())->shouldNotBeCalled();

        $this->onErrorFormatting($event);

        if ($event->isPropagationStopped()) {
            throw new RuntimeException('Expected event propagation not to be stopped.');
        }
    }
}
