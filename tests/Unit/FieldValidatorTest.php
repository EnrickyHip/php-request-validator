<?php

use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\Rules\CustomRule;
use Enricky\RequestValidator\Rules\IsDateStringRule;
use Enricky\RequestValidator\Rules\IsEmailRule;
use Enricky\RequestValidator\Rules\IsUrlRule;
use Enricky\RequestValidator\Rules\MatchRule;
use Enricky\RequestValidator\Rules\MaxRule;
use Enricky\RequestValidator\Rules\MinRule;
use Enricky\RequestValidator\Rules\TypeRule;
use Enricky\RequestValidator\Rules\ValidEnumRule;

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

it("should add type rule with strict mode", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->type(DataType::STRING);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->getStrictMode())->toBeTrue();
});

it("should add type rule with no strict mode", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->type(DataType::STRING, strict: false);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->getStrictMode())->toBeFalse();
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

it("should add email rule", function () {
    $field = new AttributeMock();
    $fieldValidator = (new FieldValidator($field))->isEmail();

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(IsEmailRule::class);
});

it("should add email rule with custom message", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->isEmail("invalid email");

    $rule = $fieldValidator->getRules()[0];
    expect($rule->getMessage())->toBe("invalid email");
});

test("isEmail() should return self", function () {
    $field = new AttributeMock("name");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->isEmail())->toBeInstanceOf(FieldValidator::class);
    expect($fieldValidator->isEmail())->toBe($fieldValidator);
});

it("should add url rule", function () {
    $field = new AttributeMock();
    $fieldValidator = (new FieldValidator($field))->isUrl();

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(IsUrlRule::class);
});

it("should add url rule with custom message", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->isUrl("invalid url");

    $rule = $fieldValidator->getRules()[0];
    expect($rule->getMessage())->toBe("invalid url");
});

test("isUrl() should return self", function () {
    $field = new AttributeMock("name");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->isUrl())->toBeInstanceOf(FieldValidator::class);
    expect($fieldValidator->isUrl())->toBe($fieldValidator);
});

it("should add match rule", function (string $match) {
    $field = new AttributeMock();
    $fieldValidator = (new FieldValidator($field))->match($match);

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(MatchRule::class);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->getMatchPattern())->toBe($match);
})->with([
    "/^[a-z]+$/i",
    "/^\d{4}-\d{2}-\d{2}$/",
    "/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/i"
]);

it("should add match rule with match message", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->match("/^[a-z]+$/i", "invalid match");

    $rule = $fieldValidator->getRules()[0];
    expect($rule->getMessage())->toBe("invalid match");
});

test("match() should return self", function () {
    $field = new AttributeMock("name");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->match("/^[a-z]+$/i"))->toBeInstanceOf(FieldValidator::class);
    expect($fieldValidator->match("/^[a-z]+$/i"))->toBe($fieldValidator);
});

it("should add date string rule", function (string $format) {
    $field = new AttributeMock();
    $fieldValidator = (new FieldValidator($field))->isDateString($format);

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(IsDateStringRule::class);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->getFormat())->toBe($format);
})->with(["d/m/Y", "m-d-Y"]);

it("should add date string rule with match message", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->isDateString(message: "invalid date string");

    $rule = $fieldValidator->getRules()[0];
    expect($rule->getMessage())->toBe("invalid date string");
});

test("isDateString() should return self", function () {
    $field = new AttributeMock("name");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->isDateString())->toBeInstanceOf(FieldValidator::class);
    expect($fieldValidator->isDateString())->toBe($fieldValidator);
});

it("should add max rule", function (int|float $max) {
    $field = new AttributeMock();
    $fieldValidator = (new FieldValidator($field))->max($max);

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(MaxRule::class);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->getMax())->toBe($max);
})->with([10, 1.1, 2, 3, 4.5, 12]);

it("should add max rule with match message", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->max(10, "maximum exceed");

    $rule = $fieldValidator->getRules()[0];
    expect($rule->getMessage())->toBe("maximum exceed");
});

test("max() should return self", function () {
    $field = new AttributeMock("name");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->max(10))->toBeInstanceOf(FieldValidator::class);
    expect($fieldValidator->max(10))->toBe($fieldValidator);
});

it("should add min rule", function (int|float $min) {
    $field = new AttributeMock();
    $fieldValidator = (new FieldValidator($field))->min($min);

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(MinRule::class);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->getMin())->toBe($min);
})->with([10, 1.1, 2, 3, 4.5, 12]);

it("should add min rule with match message", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->min(10, "minimum exceed");

    $rule = $fieldValidator->getRules()[0];
    expect($rule->getMessage())->toBe("minimum exceed");
});

test("min() should return self", function () {
    $field = new AttributeMock("name");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->min(10))->toBeInstanceOf(FieldValidator::class);
    expect($fieldValidator->min(10))->toBe($fieldValidator);
});

it("should add enum rule", function () {
    $field = new AttributeMock();
    $fieldValidator = (new FieldValidator($field))->isEnum(DataType::class);

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(ValidEnumRule::class);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->getEnumClass())->toBe(DataType::class);
});


it("should add enum rule with match message", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->isEnum(DataType::class, "invalid enum");

    $rule = $fieldValidator->getRules()[0];
    expect($rule->getMessage())->toBe("invalid enum");
});

test("isEnum() should return self", function () {
    $field = new AttributeMock("name");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->isEnum(DataType::class))->toBeInstanceOf(FieldValidator::class);
    expect($fieldValidator->isEnum(DataType::class))->toBe($fieldValidator);
});
