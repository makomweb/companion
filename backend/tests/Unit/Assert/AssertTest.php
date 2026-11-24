<?php

declare(strict_types=1);

namespace App\Tests\Unit\Assert;

use App\Assert\Assert;
use App\Assert\AssertException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AssertTest extends TestCase
{
    #[Test]
    public function that_does_not_throw_when_condition_is_true(): void
    {
        $this->expectNotToPerformAssertions();
        Assert::that(true);
    }

    #[Test]
    public function that_throws_assertion_exception_with_string_error(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage('Custom error message');

        Assert::that(false, 'Custom error message');
    }

    #[Test]
    public function that_throws_given_throwable(): void
    {
        $customException = new \RuntimeException('Custom runtime error');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Custom runtime error');

        Assert::that(false, $customException);
    }

    #[Test]
    public function that_throws_default_exception_when_no_error_given(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage('Assertion failed!');

        Assert::that(false);
    }
}
