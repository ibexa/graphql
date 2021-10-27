<?php

declare(strict_types=1);

namespace Ibexa\Spec\GraphQL\Schema\Domain;

use Ibexa\GraphQL\Schema\Domain\NameValidator;
use PhpSpec\ObjectBehavior;

final class NameValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NameValidator::class);
    }

    function it_validates_names()
    {
        $this->isValidName('777')->shouldBe(false);
        $this->isValidName('foo')->shouldBe(true);
        $this->isValidName('foo_213')->shouldBe(true);
    }
}

class_alias(NameValidatorSpec::class, 'spec\spec\Ibexa\GraphQL\Schema\Domain\NameHelperSpec');
