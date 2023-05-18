<?php

declare(strict_types=1);

use Enricky\RequestValidator\Abstract\FieldInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;

class TestValidationRuleAlternative extends ValidationRule
{
    protected string $message = "the field :fieldName with value :fieldValue is not valid";

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
    $this->testRule = new TestValidationRuleAlternative();
});

it("should return custom message", function () {
    $testRule = new TestValidationRuleAlternative("Error message");
    expect($testRule->getMessage())->toBe("Error message");
});

it("should return custom default error message", function () {
    expect($this->testRule->getMessage())->toBe("the field :fieldName with value :fieldValue is not valid");
});

it("should be a major rule", function () {
    expect($this->testRule->isMajor())->toBeTrue();
});

it("should substitute name and value params on message", function (FieldInterface $field) {
    expect($this->testRule->resolveMessage($field))->toBe("the field {$field->getName()} with value {$field->getValue()} is not valid");
})->with([
    fn () => new FieldMock("name", "Enricky"),
    fn () => new FieldMock("email", "enricky@email.com"),
]);
