<?php

use Enricky\RequestValidator\RequestValidator;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Abstract\ValidatorInterface;

/** @param string[] $errors */
function createValidator(bool $valid, array $errors, string $name = "name", mixed $value = "value")
{
    return new class($valid, $errors, $name, $value) implements ValidatorInterface
    {
        public function __construct(
            private bool $valid,
            protected array $errors,
            protected string $name,
            protected mixed $value
        ) {
        }

        public function validate(): bool
        {
            return $this->valid;
        }

        public function getErrors(): array
        {
            return $this->errors;
        }

        public function addRule(ValidationRule $rule): static
        {
            return $this;
        }

        public function getName(): string
        {
            return $this->name;
        }

        public function getValue(): mixed
        {
            return $this->value;
        }
    };
}

/** @param ValidatorInterface[] $validators */
function createRequest(
    array $validators,
    array &$data
) {
    return new class($validators, $data) extends RequestValidator
    {
        public function __construct(protected array $sentValidators, array &$data)
        {
            parent::__construct($data);
        }

        public function rules(): void
        {
            $this->validators = $this->sentValidators;
        }
    };
}

it("should validate if no validators was sent", function () {
    $data = [];
    $request = createRequest([], $data);
    expect($request->validate())->toBeTrue();
    expect($request->getErrors())->toBeArray()->toBeEmpty();
});

it("should validate if all validators are valid", function () {
    $data = [];
    $request = createRequest([
        createValidator(true, []),
        createValidator(true, []),
        createValidator(true, []),
        createValidator(true, []),
    ], $data);

    expect($request->validate())->toBeTrue();
    expect($request->getErrors())->toBeArray()->toBeEmpty();
});

it("should not validate if at least one validator is invalid", function () {
    $data = [];
    $request = createRequest([
        createValidator(true, []),
        createValidator(false, ["invalid1", "invalid2"]),
        createValidator(false, ["invalid3"]),
        createValidator(true, []),
    ], $data);

    expect($request->validate())->toBeFalse();
    expect($request->getErrors())->toEqualCanonicalizing(["invalid1", "invalid2", "invalid3"]);
});

it("should not return duplicate errors", function () {
    $data = [];
    $request = createRequest([
        createValidator(false, ["invalid1", "invalid2"]),
        createValidator(false, ["invalid1"]),
    ], $data);

    expect($request->validate())->toBeFalse();
    expect($request->getErrors())->toEqualCanonicalizing(["invalid1", "invalid2"]);
});
