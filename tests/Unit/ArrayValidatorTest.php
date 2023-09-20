<?php

use Enricky\RequestValidator\Types\DataType;
use Enricky\RequestValidator\ArrayValidator;
use Enricky\RequestValidator\Rules\IsArrayRule;
use Enricky\RequestValidator\Rules\MaxLengthRule;
use Enricky\RequestValidator\Rules\MinLengthRule;

beforeEach(function () {
    $field = new AttributeMock(value: []);
    $this->validator = new ArrayValidator($field);
});

it("should have IsArray by default", function () {
    expect($this->validator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(IsArrayRule::class);
});

it("should add type rule with array of the correct type", function (DataType|array|string $type, DataType|array $expectedType) {
    $field = new AttributeMock();
    $fieldValidator = (new ArrayValidator($field))->type($type);

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(2);

    $rule = (object)$fieldValidator->getRules()[1];
    expect($rule->getType()->getType())->toBe($expectedType);
})->with([
    [DataType::STRING, DataType::STRING],
    [DataType::INT, DataType::INT],
    ["float", DataType::FLOAT],
    [fn () => [DataType::INT, DataType::FLOAT], fn () => [DataType::INT, DataType::FLOAT]],
    [fn () => ["string", "int", "bool"], fn () => [DataType::STRING, DataType::INT, DataType::BOOL]],
]);

it("should be valid if value is array", function () {
    expect($this->validator->validate())->toBeTrue();
});

it("should not validate if value is not an array", function (mixed $value) {
    $validator = new ArrayValidator(new AttributeMock(value: $value));
    expect($validator->validate())->toBeFalse();
})->with([1, "1", true, new stdClass()]);

it("should validate normal rule for all elements in array", function () {
    $rule = createRule(fn ($value) => $value < 5);
    $validator = new ArrayValidator(new AttributeMock(value: [1, 2, 3, 4]));
    $validator->addRule($rule);
    expect($validator->validate())->toBeTrue();
});

it("should not validate normal rule if at least one of the elements are not validated", function () {
    $rule = createRule(fn ($value) => $value < 5);
    $validator = new ArrayValidator(new AttributeMock(value: [1, 2, 3, 6]));
    $validator->addRule($rule);
    expect($validator->validate())->toBeFalse();
});

it("should not validate major rule for all elements", function () {
    $rule = createRule(fn ($value) => (bool)$value, true);
    $validator = new ArrayValidator(new AttributeMock(value: [false, 0, []]));
    $validator->addRule($rule);
    expect($validator->validate())->toBeTrue();
});

it("should not validate normal rule for all elements", function () {
    $rule = createRule(fn ($value) => (bool)$value);
    $validator = new ArrayValidator(new AttributeMock(value: [false, true]));
    $validator->addRule($rule);
    expect($validator->validate())->toBeFalse();
});

it("should be valid if value is array and all rules were validated", function () {
    $this->validator
        ->addRule(createRule(true))
        ->addRule(createRule(true))
        ->addRule(createRule(true));

    expect($this->validator->validate())->toBeTrue();
});

it("should not validate if value is not an array even if all rules were validated", function (mixed $value) {
    $validator = new ArrayValidator(new AttributeMock(value: $value));

    $validator->addRule(createRule(true))
        ->addRule(createRule(true))
        ->addRule(createRule(true));

    expect($validator->validate())->toBeFalse();
})->with([1, "1", true, new stdClass()]);

it("should be valid if array is empty even with invalid rules", function () {
    $this->validator->addRule(createRule(false));
    expect($this->validator->validate())->toBeTrue();
});

it("should not be valid if at least one rule is invalid and array is not empty", function () {
    $validator = new ArrayValidator(new AttributeMock(value: [1, 2, 3]));
    $validator->addRule(createRule(true))
        ->addRule(createRule(false))
        ->addRule(createRule(true));

    expect($validator->validate())->toBeFalse();
});

it("should validate if value is null and all major rules passed (ignore all simple rules)", function () {
    $field = new AttributeMock("name", null);
    $validator = (new ArrayValidator($field))
        ->addRule(createRule(true, true))
        ->addRule(createRule(true, true))
        ->addRule(createRule(false))
        ->addRule(createRule(false));

    expect($validator->validate())->toBeTrue();
    expect($validator->getErrors())->toBeArray()->toBeEmpty();
});

it("should validate if field is required and is not null", function () {
    $this->validator->isRequired();
    expect($this->validator->validate())->toBeTrue();
});

it("should not validate if field is required but value is null", function () {
    $field = new AttributeMock("name", null);
    $validator1 = (new ArrayValidator($field))->isRequired();

    expect($validator1->validate())->toBeFalse();
    expect($validator1->getErrors())->toEqual(["field 'name' is required"]);

    $validator2 = (new ArrayValidator($field))->isRequired("custom message");

    expect($validator2->validate())->toBeFalse();
    expect($validator2->getErrors())->toEqual(["custom message"]);
});

it("should be required if condition is true", function () {
    $field = new AttributeMock("array", [1, 2, 3]);
    $nullField = new AttributeMock("array", null);

    $validator1 = (new ArrayValidator($field))->isRequiredIf(true);
    expect($validator1->validate())->toBeTrue();

    $validator2 = (new ArrayValidator($nullField))->isRequiredIf(true);
    expect($validator2->validate())->toBeFalse();
    expect($validator2->getErrors())->toEqual(["field 'array' is required"]);

    $validator3 = (new ArrayValidator($nullField))->isRequiredIf(true, "custom message");
    expect($validator3->validate())->toBeFalse();
    expect($validator3->getErrors())->toEqual(["custom message"]);
});

it("should not be required if condition is false", function () {
    $field = new AttributeMock("array", [1, 2, 3]);
    $nullField = new AttributeMock("array", null);

    $validator1 = (new ArrayValidator($nullField))->isRequiredIf(false);
    expect($validator1->validate())->toBeTrue();

    $validator2 = (new ArrayValidator($field))->isRequiredIf(false);
    expect($validator2->validate())->toBeTrue();
});

it("should be prohibited if condition is true", function () {
    $field = new AttributeMock("array", [1, 2, 3]);
    $nullField = new AttributeMock("array", null);

    $validator1 = (new ArrayValidator($field))->prohibitedIf(true);
    expect($validator1->validate())->toBeFalse();
    expect($validator1->getErrors())->toEqual(["field 'array' is prohibited"]);

    $validator2 = (new ArrayValidator($field))->prohibitedIf(true, "custom message");
    expect($validator2->validate())->toBeFalse();
    expect($validator2->getErrors())->toEqual(["custom message"]);

    $validator3 = (new ArrayValidator($nullField))->prohibitedIf(true);
    expect($validator3->validate())->toBeTrue();
});

it("should not be prohibited if condition is false", function () {
    $field = new AttributeMock("array", [1, 2, 3]);
    $nullField = new AttributeMock("array", null);

    $validator1 = (new ArrayValidator($field))->prohibitedIf(false);
    expect($validator1->validate())->toBeTrue();

    $validator2 = (new ArrayValidator($nullField))->prohibitedIf(false);
    expect($validator2->validate())->toBeTrue();
});

it("should add MaxLengthRule", function () {
    $this->validator->maxLength(10);

    expect($this->validator->getRules())
        ->toBeArray()
        ->toHaveLength(2);

    expect($this->validator->getRules()[1])->toBeInstanceOf(MaxLengthRule::class);
});

it("should add MaxLengthRule with custom message", function () {
    $this->validator->maxLength(10, "max length");

    $rule =  $this->validator->getRules()[1];
    expect($rule->getMessage())->toBe("max length");
});


test("maxLength() should return self", function () {
    $field = new AttributeMock();
    $arrayValidator = new ArrayValidator($field);

    expect($arrayValidator->maxLength(10))->toBeInstanceOf(ArrayValidator::class);
    expect($arrayValidator->maxLength(10))->toBe($arrayValidator);
});

it("should add MinLengthRule", function () {
    $this->validator->minLength(10);

    expect($this->validator->getRules())
        ->toBeArray()
        ->toHaveLength(2);

    expect($this->validator->getRules()[1])->toBeInstanceOf(MinLengthRule::class);
});

it("should add MinLengthRule with custom message", function () {
    $this->validator->minLength(10, "min length");

    $rule =  $this->validator->getRules()[1];
    expect($rule->getMessage())->toBe("min length");
});


test("minLength() should return self", function () {
    $field = new AttributeMock();
    $arrayValidator = new ArrayValidator($field);

    expect($arrayValidator->minLength(10))->toBeInstanceOf(ArrayValidator::class);
    expect($arrayValidator->minLength(10))->toBe($arrayValidator);
});

it("should replace :value with [array] in message on major rules", function () {
    $this->validator->minLength(10, ":value does not have a min length");
    expect($this->validator->getErrors()[0])->toBe("[array] does not have a min length");
});

it("should replace :value with element value in message when rule is not major", function () {
    $field = new AttributeMock(value: [8, 11, 12, 9]);
    $validator = (new ArrayValidator($field))->max(10, ":value is bigger than max value :max");
    expect($validator->getErrors()[0])->toBe("11 is bigger than max value 10");
});
