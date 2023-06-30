<?php

use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\Rules\ValidateOrRule;
use Enricky\RequestValidator\Validator;

beforeEach(function () {
    $field = new AttributeMock();
    $this->validator = new class($field) extends Validator
    {
    };
});

it("should get field name and value", function () {
    $field = new AttributeMock();
    $validator = new FieldValidator($field);
    expect($validator->getName())->toBe($field->getName());
    expect($validator->getValue())->toBe($field->getValue());
});

it("should be valid if no rule was sent", function () {
    expect($this->validator->validate())->toBeTrue();
});

it("should be valid if all rules were validated", function () {
    $this->validator
        ->addRule(createRule(true))
        ->addRule(createRule(true))
        ->addRule(createRule(true));

    expect($this->validator->validate())->toBeTrue();
});

it("should return all rules (major or not)", function () {
    $rule1 = createRule(true, true);
    $rule2 = createRule(true);
    $rule3 = createRule(true);

    $this->validator->addRule($rule1)->addRule($rule2)->addRule($rule3);
    expect($this->validator->getRules())->toEqualCanonicalizing([$rule1, $rule2, $rule3]);
});

it("should not be valid if at least one rule is invalid", function () {
    $this->validator
        ->addRule(createRule(true))
        ->addRule(createRule(true))
        ->addRule(createRule(false))
        ->addRule(createRule(true))
        ->addRule(createRule(true));

    expect($this->validator->validate())->toBeFalse();
});

it("should return no errors if no rule was sent", function () {
    expect($this->validator->getErrors())->toBeArray()->toBeEmpty();
});

it("should return no errors if all rules were valid", function () {
    $this->validator
        ->addRule(createRule(true))
        ->addRule(createRule(true))
        ->addRule(createRule(true));

    expect($this->validator->getErrors())->toBeArray()->toBeEmpty();
});

it("should return resolved errors", function () {
    $this->validator
        ->addRule(createRule(false))
        ->addRule(new ValidationRuleWithParams("test", "otherTest"));

    expect($this->validator->getErrors())->toEqualCanonicalizing(
        [
            "the field 'name' with value 'value' is not valid with 'test' and 'otherTest'",
            "field 'name' is invalid",
        ]
    );
});

it("should only show one message if a major rule is invalid", function () {
    $this->validator
        ->addRule(createRule(true, true))
        ->addRule(createRule(false, true))
        ->addRule(new ValidationRuleWithParams("param", "otherParam"))
        ->addRule(createRule(false))
        ->addRule(createRule(false));

    expect($this->validator->getErrors())->toEqual(["field 'name' is invalid"]);
});


it("should validate if value is null and all major rules passed (ignore all simple rules)", function () {
    $field = new AttributeMock("name", null);
    $validator = (new FieldValidator($field))
        ->addRule(createRule(true, true))
        ->addRule(createRule(true, true))
        ->addRule(createRule(false))
        ->addRule(createRule(false));

    expect($validator->validate())->toBeTrue();
    expect($validator->getErrors())->toBeArray()->toBeEmpty();
});

it("should validate if field is required and is not null", function () {
    $field = new AttributeMock("name", "value");
    $validator = (new FieldValidator($field))->isRequired();

    expect($validator->validate())->toBeTrue();
});

test("isRequired should return self", function () {
    $field = new AttributeMock("name", "value");
    $validator = new FieldValidator($field);
    expect($validator->isRequired())->toBe($validator);
});

it("should not validate if field is required but value is null", function () {
    $field = new AttributeMock("name", null);
    $validator1 = (new FieldValidator($field))->isRequired();

    expect($validator1->validate())->toBeFalse();
    expect($validator1->getErrors())->toEqual(["field 'name' is required"]);

    $validator2 = (new FieldValidator($field))->isRequired("custom message");

    expect($validator2->validate())->toBeFalse();
    expect($validator2->getErrors())->toEqual(["custom message"]);
});

it("should be required if condition is true", function () {
    $field = new AttributeMock("name", "value");
    $nullField = new AttributeMock("name", null);

    $validator1 = (new FieldValidator($field))->isRequiredIf(true);
    expect($validator1->validate())->toBeTrue();

    $validator2 = (new FieldValidator($nullField))->isRequiredIf(true);
    expect($validator2->validate())->toBeFalse();
    expect($validator2->getErrors())->toEqual(["field 'name' is required"]);

    $validator3 = (new FieldValidator($nullField))->isRequiredIf(true, "custom message");
    expect($validator3->validate())->toBeFalse();
    expect($validator3->getErrors())->toEqual(["custom message"]);
});

it("should not be required if condition is false", function () {
    $field = new AttributeMock("name", "value");
    $nullField = new AttributeMock("name", null);

    $validator1 = (new FieldValidator($nullField))->isRequiredIf(false);
    expect($validator1->validate())->toBeTrue();

    $validator2 = (new FieldValidator($field))->isRequiredIf(false);
    expect($validator2->validate())->toBeTrue();
});

test("isRequiredIf should return self", function () {
    $field = new AttributeMock("name", "value");
    $validator = new FieldValidator($field);

    expect($validator->isRequiredIf(true))->toBe($validator);
});

it("should be prohibited if condition is true", function () {
    $field = new AttributeMock("name", "value");
    $nullField = new AttributeMock("name", null);

    $validator1 = (new FieldValidator($field))->prohibitedIf(true);
    expect($validator1->validate())->toBeFalse();
    expect($validator1->getErrors())->toEqual(["field 'name' is prohibited"]);

    $validator2 = (new FieldValidator($field))->prohibitedIf(true, "custom message");
    expect($validator2->validate())->toBeFalse();
    expect($validator2->getErrors())->toEqual(["custom message"]);

    $validator3 = (new FieldValidator($nullField))->prohibitedIf(true);
    expect($validator3->validate())->toBeTrue();
});

it("should not be prohibited if condition is false", function () {
    $field = new AttributeMock("name", "value");
    $nullField = new AttributeMock("name", null);

    $validator1 = (new FieldValidator($field))->prohibitedIf(false);
    expect($validator1->validate())->toBeTrue();

    $validator2 = (new FieldValidator($nullField))->prohibitedIf(false);
    expect($validator2->validate())->toBeTrue();
});

it("should add ValidateOr rule", function () {
    $field = new AttributeMock();
    $rules = [];

    $fieldValidator = (new FieldValidator($field))->validateOr([]);

    expect($fieldValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(ValidateOrRule::class);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->getRules())->toBe($rules);
});

it("should add ValidateOr rule with custom message", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->validateOr([], "invalid field");

    $rule = $fieldValidator->getRules()[0];
    expect($rule->getMessage())->toBe("invalid field");
});

it("should add ValidateOr non exclusive as default", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->validateOr([]);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->isExclusive())->toBeFalse();
});

it("should add ValidateOr and set exclusive", function () {
    $field = new AttributeMock("name");
    $fieldValidator = (new FieldValidator($field))->validateOr([], exclusive: true);

    $rule = (object)$fieldValidator->getRules()[0];
    expect($rule->isExclusive())->toBeTrue();
});

test("validateOr() should return self", function () {
    $field = new AttributeMock("name");
    $fieldValidator = new FieldValidator($field);

    expect($fieldValidator->validateOr([]))->toBeInstanceOf(FieldValidator::class);
    expect($fieldValidator->validateOr([]))->toBe($fieldValidator);
});
