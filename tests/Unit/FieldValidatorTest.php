<?php

use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\Rules\CustomRule;
use Enricky\RequestValidator\Rules\TypeRule;

beforeEach(function () {
    $field = new AttributeMock();
    $this->fieldValidator = new FieldValidator($field);
});

it("should add type rule with correct type", function (DataType $type) {
    $field = new AttributeMock();
    $fieldValidator = (new FieldValidator($field))->type($type);

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(TypeRule::class);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->getType())->toBe($type);
})->with([DataType::STRING, DataType::INT, DataType::FLOAT]);

it("should add type rule with custom message", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->type(DataType::STRING, "incorrect type");

    $rule = $fieldValidator->getRules()[0];
    expect($rule->getMessage())->toBe("incorrect type");
});

test("type() should return self", function () {
    $field = new AttributeMock("name");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->type(DataType::BOOL))->toBeInstanceOf(FieldValidator::class);
    expect($fieldValidator->type(DataType::BOOL))->toBe($fieldValidator);
});

it("should add custom rule", function (Closure $condition) {
    $field = new AttributeMock();
    $fieldValidator = (new FieldValidator($field))->custom($condition);

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(CustomRule::class);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->getCondition())->toBe($condition);
})->with([
    fn () => fn () => true,
    fn () => fn () => false,
    fn () => fn () => 1 == "1",
]);

it("should add custom rule with custom message", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->custom(fn () => false, "invalid");

    $rule = $fieldValidator->getRules()[0];
    expect($rule->getMessage())->toBe("invalid");
});

test("custom() should return self", function () {
    $field = new AttributeMock("name");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->custom(fn () => true))->toBeInstanceOf(FieldValidator::class);
    expect($fieldValidator->custom(fn () => true))->toBe($fieldValidator);
});
