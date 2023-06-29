<?php

declare(strict_types=1);

use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;

class MajorTestValidationRule extends ValidationRule
{
    protected string $message = "the field :name with value :value is not valid";

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
    $this->testRule = new MajorTestValidationRule();
});

it("should return custom message", function () {
    $testRule = new MajorTestValidationRule("Error message");
    expect($testRule->getMessage())->toBe("Error message");
});

it("should return custom default error message", function () {
    expect($this->testRule->getMessage())->toBe("the field :name with value :value is not valid");
});

it("should be a major rule", function () {
    expect($this->testRule->isMajor())->toBeTrue();
});

it("should replace name and value params on message", function (AttributeInterface $field) {
    $fieldName = $field->getName();
    $fieldValue = $field->getValue();

    expect($this->testRule->resolveMessage($field))->toBe("the field '$fieldName' with value '$fieldValue' is not valid");
})->with([
    fn () => new AttributeMock("name", "Enricky"),
    fn () => new AttributeMock("email", "enricky@email.com"),
]);

it("should replace non string value to a string representation", function (AttributeInterface $field, string $representation) {
    $fieldName = $field->getName();

    expect($this->testRule->resolveMessage($field))->toBe("the field '$fieldName' with value $representation is not valid");
})->with([
    [new AttributeMock("int", 1), "1"],
    [new AttributeMock("float", 10.5), "10.5"],
    [new AttributeMock("true", true), "true"],
    [new AttributeMock("false", false), "false"],
    [new AttributeMock("array", []), "[array]"],
    [new AttributeMock("object", new stdClass()), "{object}"],
    [new AttributeMock("null", null), "null"],
]);
