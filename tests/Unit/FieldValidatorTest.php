<?php

use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\FieldValidator;
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

    expect($fieldValidator->type(DataType::BOOL))->toBe($fieldValidator);
});
