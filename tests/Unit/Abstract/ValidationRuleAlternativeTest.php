<?php

declare(strict_types=1);

use Enricky\RequestValidator\Abstract\ValidationRule;

class TestWithDefaultMessageRule extends ValidationRule
{
    protected string $message = "custom default message";

    public function validate(mixed $value): bool
    {
        return $value === "valid value";
    }

    public function isMajor(): bool
    {
        return true;
    }
}

beforeEach(function () {
    $this->testRule = new TestWithDefaultMessageRule();
});

it("should return custom default error message", function () {
    expect($this->testRule->getMessage())->toBe("custom default message");
});

it("should return custom message", function () {
    $testRule = new TestWithDefaultMessageRule("Error message");
    expect($testRule->getMessage())->toBe("Error message");
});
