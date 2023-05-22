<?php

use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\Rules\TypeRule;

beforeEach(function () {
    $field = new FieldMock();
    $this->fieldValidator = new FieldValidator($field);
});

function createRule(bool $valid, bool $isMajor = false): ValidationRule
{
    return new class($valid, $isMajor) extends ValidationRule
    {
        public function __construct(
            private bool $valid,
            private bool $isMajor,
        ) {
        }

        public function validate(mixed $value): bool
        {
            return $this->valid;
        }

        public function isMajor(): bool
        {
            return $this->isMajor;
        }
    };
}

it("should get field", function () {
    $field = new FieldMock();
    $fieldValidator = new FieldValidator($field);
    expect($fieldValidator->getField())->toBe($field);
});

it("should be valid if no rule was sent", function () {
    expect($this->fieldValidator->validate())->toBeTrue();
});

it("should be valid if all rules were validated", function () {
    $this->fieldValidator
        ->addRule(createRule(true))
        ->addRule(createRule(true))
        ->addRule(createRule(true));

    expect($this->fieldValidator->validate())->toBeTrue();
});

it("should not be valid if at least one rule is invalid", function () {
    $this->fieldValidator
        ->addRule(createRule(true))
        ->addRule(createRule(true))
        ->addRule(createRule(false))
        ->addRule(createRule(true))
        ->addRule(createRule(true));

    expect($this->fieldValidator->validate())->toBeFalse();
});

it("should return no errors if no rule was sent", function () {
    expect($this->fieldValidator->getErrors())->toBeArray()->toBeEmpty();
});

it("should return no errors if all rules were valid", function () {
    $this->fieldValidator
        ->addRule(createRule(true))
        ->addRule(createRule(true))
        ->addRule(createRule(true));

    expect($this->fieldValidator->getErrors())->toBeArray()->toBeEmpty();
});

it("should return resolved errors", function () {
    $this->fieldValidator
        ->addRule(createRule(false))
        ->addRule(new ValidationRuleWithParams("test", "otherTest"));

    expect($this->fieldValidator->getErrors())->toEqualCanonicalizing(
        [
            "the field 'name' with value 'value' is not valid with 'test' and 'otherTest'",
            "field 'name' is invalid",
        ]
    );
});

it("should only show one message if a major rule is invalid", function () {
    $this->fieldValidator
        ->addRule(createRule(true, true))
        ->addRule(createRule(false, true))
        ->addRule(new ValidationRuleWithParams("param", "otherParam"))
        ->addRule(createRule(false))
        ->addRule(createRule(false));

    expect($this->fieldValidator->getErrors())->toEqual(["field 'name' is invalid"]);
});


it("should validate if value is null and all major rules passed (ignore all simple rules)", function () {
    $field = new FieldMock("name", null);
    $fieldValidator = (new FieldValidator($field))
        ->addRule(createRule(true, true))
        ->addRule(createRule(true, true))
        ->addRule(createRule(false))
        ->addRule(createRule(false));

    expect($fieldValidator->validate())->toBeTrue();
    expect($fieldValidator->getErrors())->toBeArray()->toBeEmpty();
});

it("should validate if field is required and is not null", function () {
    $field = new FieldMock("name", "value");
    $fieldValidator = (new FieldValidator($field))->isRequired();

    expect($fieldValidator->validate())->toBeTrue();
});

test("isRequired should return self", function () {
    $field = new FieldMock("name", "value");
    $fieldValidator = new FieldValidator($field);
    expect($fieldValidator->isRequired())->toBe($fieldValidator);
});

it("should not validate if field is required but value is null", function () {
    $field = new FieldMock("name", null);
    $fieldValidator1 = (new FieldValidator($field))->isRequired();

    expect($fieldValidator1->validate())->toBeFalse();
    expect($fieldValidator1->getErrors())->toEqual(["field 'name' is required"]);

    $fieldValidator2 = (new FieldValidator($field))->isRequired("custom message");

    expect($fieldValidator2->validate())->toBeFalse();
    expect($fieldValidator2->getErrors())->toEqual(["custom message"]);
});

it("should be required if condition is true", function () {
    $field = new FieldMock("name", "value");
    $nullField = new FieldMock("name", null);

    $fieldValidator1 = (new FieldValidator($field))->isRequiredIf(true);
    expect($fieldValidator1->validate())->toBeTrue();

    $fieldValidator2 = (new FieldValidator($nullField))->isRequiredIf(true);
    expect($fieldValidator2->validate())->toBeFalse();
    expect($fieldValidator2->getErrors())->toEqual(["field 'name' is required"]);

    $fieldValidator3 = (new FieldValidator($nullField))->isRequiredIf(true, "custom message");
    expect($fieldValidator3->validate())->toBeFalse();
    expect($fieldValidator3->getErrors())->toEqual(["custom message"]);
});

it("should not be required if condition is false", function () {
    $field = new FieldMock("name", "value");
    $nullField = new FieldMock("name", null);

    $fieldValidator1 = (new FieldValidator($nullField))->isRequiredIf(false);
    expect($fieldValidator1->validate())->toBeTrue();

    $fieldValidator2 = (new FieldValidator($field))->isRequiredIf(false);
    expect($fieldValidator2->validate())->toBeTrue();
});

test("isRequiredIf should return self", function () {
    $field = new FieldMock("name", "value");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->isRequiredIf(true))->toBe($fieldValidator);
});

it("should be prohibited if condition is true", function () {
    $field = new FieldMock("name", "value");
    $nullField = new FieldMock("name", null);

    $fieldValidator1 = (new FieldValidator($field))->prohibitedIf(true);
    expect($fieldValidator1->validate())->toBeFalse();
    expect($fieldValidator1->getErrors())->toEqual(["field 'name' is prohibited"]);

    $fieldValidator2 = (new FieldValidator($field))->prohibitedIf(true, "custom message");
    expect($fieldValidator2->validate())->toBeFalse();
    expect($fieldValidator2->getErrors())->toEqual(["custom message"]);

    $fieldValidator3 = (new FieldValidator($nullField))->prohibitedIf(true);
    expect($fieldValidator3->validate())->toBeTrue();
});

it("should not be prohibited if condition is false", function () {
    $field = new FieldMock("name", "value");
    $nullField = new FieldMock("name", null);

    $fieldValidator1 = (new FieldValidator($field))->prohibitedIf(false);
    expect($fieldValidator1->validate())->toBeTrue();

    $fieldValidator2 = (new FieldValidator($nullField))->prohibitedIf(false);
    expect($fieldValidator2->validate())->toBeTrue();
});

it("should validate if type is correct", function (DataType|string $type, mixed $value) {
    $field = new FieldMock("name", $value);
    $fieldValidator = (new FieldValidator($field))->type($type);

    expect($fieldValidator->validate())->toBeTrue();
})->with("correct_types");

it("should not validate if type is incorrect with default message", function (DataType|string $type, mixed $value) {
    $field = new FieldMock("name", $value);
    $fieldValidator = (new FieldValidator($field))->type($type);

    $typeName = $type instanceof DataType ? $type->value : strtolower($type);

    expect($fieldValidator->validate())->toBeFalse();
    expect($fieldValidator->getErrors())->toEqual(["field 'name' is not of type '$typeName'"]);
})->with("incorrect_types");

it("should not validate if type is incorrect with custom message", function (DataType|string $type, mixed $value) {
    $field = new FieldMock("name", $value);
    $fieldValidator = (new FieldValidator($field))->type($type, "incorrect type");

    expect($fieldValidator->validate())->toBeFalse();
    expect($fieldValidator->getErrors())->toEqual(["incorrect type"]);
})->with("incorrect_types");
