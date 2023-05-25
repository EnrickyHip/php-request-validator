<?php

use Enricky\RequestValidator\Abstract\Request;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Abstract\ValidatorInterface;

/** @param string[] $errors */
function createValidator(bool $valid, array $errors)
{
    return new class($valid, $errors) implements ValidatorInterface
    {
        public function __construct(private bool $valid, private array $errors)
        {
        }

        public function validate(): bool
        {
            return $this->valid;
        }

        public function getErrors(): array
        {
            return $this->errors;
        }

        public function addRule(ValidationRule $rule): self
        {
            return $this;
        }
    };
}

/** @param ValidatorInterface[] $validators */
function createRequest(array $validators, array $data)
{
    return new class($validators, $data) extends Request
    {
        public function __construct(private array $validators, array $data)
        {
            parent::__construct($data);
        }

        public function rules(): array
        {
            return $this->validators;
        }
    };
}

it("should validate if no validators was sent", function () {
    $request = createRequest([], []);
    expect($request->validate())->toBeTrue();
    expect($request->getErrors())->toBeArray()->toBeEmpty();
});

it("should validate if all validators are valid", function () {
    $request = createRequest([
        createValidator(true, []),
        createValidator(true, []),
        createValidator(true, []),
        createValidator(true, []),
    ], []);

    expect($request->validate([]))->toBeTrue();
    expect($request->getErrors([]))->toBeArray()->toBeEmpty();
});

it("should not validate if at least on validator is invalid", function () {
    $request = createRequest([
        createValidator(true, []),
        createValidator(false, ["invalid1", "invalid2"]),
        createValidator(false, ["invalid3"]),
        createValidator(true, []),
    ], []);

    expect($request->validate([]))->toBeFalse();
    expect($request->getErrors([]))->toEqualCanonicalizing(["invalid1", "invalid2", "invalid3"]);
});

it("should not return duplicate errors", function () {
    $request = createRequest([
        createValidator(false, ["invalid1", "invalid2"]),
        createValidator(false, ["invalid1"]),
    ], []);

    expect($request->validate([]))->toBeFalse();
    expect($request->getErrors([]))->toEqualCanonicalizing(["invalid1", "invalid2"]);
});

// it("should create field validators", function () {
//     $request = createRequest([
//         createValidator(false, ["invalid1", "invalid2"]),
//         createValidator(false, ["invalid1"]),
//     ], ["name" => "Enricky"]);

//     $validator1 = $request->validateField("name");
//     $validator2 = $request->validateField("email");

//     expect([$validator1, $validator2])->toContainOnlyInstancesOf(FieldValidator::class);
// });

// it("should create file validators", function () {
//     $request = createRequest(
//         [
//             createValidator(false, ["invalid1", "invalid2"]),
//             createValidator(false, ["invalid1"]),
//         ],
//         ["file" => [
//             "name" => "name.png",
//             "full_path" => "name.png",
//             "tmp_name" => "C:\\xampp\\tmp\\php8BC8.tmp",
//             "error" => 0,
//             "size" => 34620
//         ]]
//     );

//     $validator1 = $request->validateFile("file");
//     $validator2 = $request->validateFile("file2");

//     expect([$validator1, $validator2])->toContainOnlyInstancesOf(FileValidator::class);
// });
