<?php

declare(strict_types=1);

use Enricky\RequestValidator\Abstract\ValidationRule;

class TestMajorRule extends ValidationRule
{
    public function validate(mixed $value): bool
    {
        return $value === "valid value";
    }

    public function isMajor(): bool
    {
        return true;
    }
}

it("should be a major rule", function () {
    $testRule = new TestMajorRule("Error message");
    expect($testRule->isMajor())->toBeTrue();
});
