<?php

declare(strict_types=1);

use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;

class TestRule extends ValidationRule
{
    public function validate(mixed $value): bool
    {
        return $value === "valid value";
    }
}

beforeEach(function () {
    $this->testRule = new TestRule();
});

it("should return custom message if sent", function () {
    $testRule = new TestRule("Error message");
    expect($testRule->getMessage())->toBe("Error message");
});

it("should return default error message if not sent", function () {
    expect($this->testRule->getMessage())->toBe("field :name is invalid");
});

it("should return false when validation fails", function () {
    expect($this->testRule->validate("invalid value"))->toBeFalse();
});

it("should return true when validation fails", function () {
    expect($this->testRule->validate("valid value"))->toBeTrue();
});


it("should replace name param on message", function (AttributeInterface $field) {
    $fieldName = $field->getName();
    expect($this->testRule->resolveMessage($field))->toBe("field '$fieldName' is invalid");
})->with([
    fn () => new AttributeMock("name", "Enricky"),
    fn () => new AttributeMock("email", "enricky@email.com"),
]);
