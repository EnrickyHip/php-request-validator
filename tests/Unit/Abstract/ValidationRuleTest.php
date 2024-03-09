<?php

declare(strict_types=1);

use Enricky\RequestValidator\Abstract\AttributeInterface;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;

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


it("should replace :name param on message", function (AttributeInterface $field) {
    $fieldName = $field->getName();
    expect($this->testRule->resolveMessage($field))->toBe("field '$fieldName' is invalid");
})->with([
    fn () => new AttributeMock("name", "Enricky"),
    fn () => new AttributeMock("email", "enricky@email.com"),
]);

it("should replace :name param on message for array", function (AttributeInterface $field, int|string $key) {
    $fieldName = $field->getName();
    expect($this->testRule->resolveArrayMessage($field, $key))->toBe("field '$fieldName' is invalid");
})->with([
    fn () => [new AttributeMock("name", [1, 2, 3]), 0],
    fn () => [new AttributeMock("email", ["key" => "value"]), "key"],
]);

it("should replace :value param on message", function (mixed $value, string $resolvedValue) {
    $attribute = new AttributeMock("value", $value);
    $rule = createRule(false, false, "value :value is invalid");
    expect($rule->resolveMessage($attribute))->toBe("value $resolvedValue is invalid");
})->with([
    ["Enricky", "'Enricky'"],
    [1, "1"],
    [1.2, "1.2"],
    [null, "null"],
    [true, "true"],
    [false, "false"],
    [[1, 2, 3], "[array]"],
    [new stdClass(), "{object}"],
]);

it("should throw InvalidDataTypeException if sent an attribute if non array value", function (AttributeInterface $attribute) {
    $rule = createRule(false, false, "value :value is invalid");
    $closure = fn () => $rule->resolveArrayMessage($attribute, 0);
    expect($closure)->toThrow(InvalidDataTypeException::class);
})->with([
    new AttributeMock("name", "value"),
    new AttributeMock("name", 1),
    new AttributeMock("name", 1.2),
    new AttributeMock("name", true),
    new AttributeMock("name", new stdClass()),
]);

it("should replace :value param with element value on resolveArrayMessage", function (array $value, string $resolvedValue, int|string $index) {
    $attribute = new AttributeMock("value", $value);
    $rule = createRule(false, false, "value :value is invalid");
    expect($rule->resolveArrayMessage($attribute, $index))->toBe("value $resolvedValue is invalid");
})->with([
    [
        ["Enricky", "Test"],
        "'Enricky'",
        0
    ],
    [
        [3, 2, 1],
        "1",
        2
    ],
    [
        ["zero" => 0, "bigger" => 1.2, "minor" => -1.2],
        "1.2",
        "bigger"
    ],
    [
        [[], null, 2],
        "null",
        1
    ],
    [
        [true, false, true],
        "true",
        2
    ],
    [
        [true, false, true],
        "true",
        2
    ],
    [
        [[1, 2, 3], 4, 5],
        "[array]",
        0
    ],
    [
        ["object" => new stdClass(), "string" => "value", "int" => 0],
        "{object}",
        "object"
    ],
]);
