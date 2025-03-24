<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace spec\Ibexa\GraphQL\Schema\Domain;

use Ibexa\GraphQL\Schema\Domain\NameValidator;
use PhpSpec\ObjectBehavior;

final class NameValidatorSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(NameValidator::class);
    }

    public function it_validates_names(): void
    {
        $this->isValidName('777')->shouldBe(false);
        $this->isValidName('foo')->shouldBe(true);
        $this->isValidName('foo_213')->shouldBe(true);
    }
}
