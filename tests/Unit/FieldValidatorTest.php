<?php

use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\Rules\CustomRule;
use Enricky\RequestValidator\Rules\IsEmailRule;
use Enricky\RequestValidator\Rules\IsUrlRule;
use Enricky\RequestValidator\Rules\MatchRule;
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
