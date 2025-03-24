<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace spec\Ibexa\GraphQL\Schema;

use Ibexa\GraphQL\Schema;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Ibexa\GraphQL\Tools\Stubs\InitializableWorker;

class GeneratorSpec extends ObjectBehavior
{
    public function let(
        Schema\Builder $schema,
        Schema\Domain\Iterator $iterator,
        Schema\Domain\Iterator $iterator2,
        InitializableWorker $worker1,
        Schema\Worker $worker2
    ): void {
        $iterator->iterate()->will(static function () { yield []; });
        $iterator->init($schema)->willReturn(null);

        $iterator2->iterate()->will(static function () { yield []; });
        $iterator2->init($schema)->willReturn(null);

        $schema->getSchema()->willReturn([]);

        $worker1->canWork($schema, Argument::any())->willReturn(false);
        $worker1->init($schema)->willReturn(null);
        $worker2->canWork($schema, Argument::any())->willReturn(false);

        $this->beConstructedWith($schema, [$iterator, $iterator2], [$worker1, $worker2]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Schema\Generator::class);
    }

    public function it_calls_init_on_each_iterator(
        Schema\Builder $schema,
        Schema\Domain\Iterator $iterator,
        Schema\Domain\Iterator $iterator2
    ): void {
        $iterator->init($schema)->shouldBeCalled();
        $iterator->iterate()->shouldBeCalled();

        $iterator2->init($schema)->shouldBeCalled();
        $iterator2->iterate()->shouldBeCalled();

        $this->generate();
    }

    public function it_passes_arguments_from_iterators_to_workers(
        Schema\Domain\Iterator $iterator,
        Schema\Worker $worker1,
        Schema\Worker $worker2,
        Schema\Builder $schema
    ): void {
        $arguments = [
            ['1_arg' => 'value'],
            ['2_arg' => 'value'],
        ];
        $iterator->iterate()->will(static function () use ($arguments) {
            foreach($arguments as $args) {
                yield $args;
            }
        });

        $worker1->canWork($schema, $arguments[0])->willReturn(true);
        $worker1->work($schema, $arguments[0])->shouldBeCalled();

        $worker2->canWork($schema, $arguments[0])->willReturn(false);
        $worker2->work($schema, $arguments[0])->shouldNotBeCalled();

        $worker1->canWork($schema, $arguments[1])->willReturn(false);
        $worker1->work($schema, $arguments[1])->shouldNotBeCalled();

        $worker2->canWork($schema, $arguments[1])->willReturn(true);
        $worker2->work($schema, $arguments[1])->shouldBeCalled();

        // iterator 2 and its empty array
        $worker1->canWork($schema, [])->willReturn(false);
        $worker1->work($schema, [])->shouldNotBeCalled();

        $worker2->canWork($schema, [])->willReturn(false);
        $worker2->work($schema, [])->shouldNotBeCalled();

        $this->generate();
    }

    public function it_calls_init_on_schema_initializer_workers(
        Schema\Builder $schema,
        InitializableWorker $worker1
    ): void {
        $worker1->init($schema)->shouldBeCalled();
        $this->generate();
    }
}
