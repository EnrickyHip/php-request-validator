<?php

use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\FieldValidator;

beforeEach(function () {
    $field = new AttributeMock();
    $this->fieldValidator = new FieldValidator($field);
});

it("should validate if type is correct", function (DataType|string $type, mixed $value) {
    $field = new AttributeMock("name", $value);
    $fieldValidator = (new FieldValidator($field))->type($type);

    expect($fieldValidator->validate())->toBeTrue();
})->with("correct_types");

it("should not validate if type is incorrect with default message", function (DataType|string $type, mixed $value) {
    $field = new AttributeMock("name", $value);
    $fieldValidator = (new FieldValidator($field))->type($type);

    $typeName = $type instanceof DataType ? $type->value : strtolower($type);

    expect($fieldValidator->validate())->toBeFalse();
    expect($fieldValidator->getErrors())->toEqual(["field 'name' is not of type '$typeName'"]);
})->with("incorrect_types");

it("should not validate if type is incorrect with custom message", function (DataType|string $type, mixed $value) {
    $field = new AttributeMock("name", $value);
    $fieldValidator = (new FieldValidator($field))->type($type, "incorrect type");

    expect($fieldValidator->validate())->toBeFalse();
    expect($fieldValidator->getErrors())->toEqual(["incorrect type"]);
})->with("incorrect_types");
