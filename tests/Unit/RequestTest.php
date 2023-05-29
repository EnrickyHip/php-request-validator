<?php

use Enricky\RequestValidator\Abstract\Request;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Abstract\ValidatorInterface;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\File;
use Enricky\RequestValidator\FileValidator;

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

        public function addRule(ValidationRule $rule): static
        {
            return $this;
        }
    };
}

/** @param ValidatorInterface[] $validators */
function createRequest(array $validators, array &$data)
{
    return new class($validators, $data) extends Request
    {
        public function __construct(private array $validators, array &$data)
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

    expect($request->validate([]))->toBeTrue();
    expect($request->getErrors([]))->toBeArray()->toBeEmpty();
});

it("should not validate if at least on validator is invalid", function () {
    $data = [];
    $request = createRequest([
        createValidator(true, []),
        createValidator(false, ["invalid1", "invalid2"]),
        createValidator(false, ["invalid3"]),
        createValidator(true, []),
    ], $data);

    expect($request->validate([]))->toBeFalse();
    expect($request->getErrors([]))->toEqualCanonicalizing(["invalid1", "invalid2", "invalid3"]);
});

it("should not return duplicate errors", function () {
    $data = [];
    $request = createRequest([
        createValidator(false, ["invalid1", "invalid2"]),
        createValidator(false, ["invalid1"]),
    ], $data);

    expect($request->validate([]))->toBeFalse();
    expect($request->getErrors([]))->toEqualCanonicalizing(["invalid1", "invalid2"]);
});

it("should create field validators", function () {
    $data = ["name" => "Enricky"];
    $request = createRequest([
        createValidator(false, ["invalid1", "invalid2"]),
        createValidator(false, ["invalid1"]),
    ], $data);

    $validator1 = $request->validateField("name");
    $validator2 = $request->validateField("email");

    expect([$validator1, $validator2])->toContainOnlyInstancesOf(FieldValidator::class);
    expect($validator1->getAttribute()->getName())->toBe("name");
    expect($validator1->getAttribute()->getValue())->toBe("Enricky");
});

it("should create file validators", function () {
    $data = [
        "file" => [
            "name" => "name.png",
            "full_path" => "name.png",
            "tmp_name" => "C:\\xampp\\tmp\\php8BC8.tmp",
            "error" => 0,
            "size" => 34620
        ]
    ];

    $request = createRequest(
        [
            createValidator(false, ["invalid1", "invalid2"]),
            createValidator(false, ["invalid1"]),
        ],
        $data
    );

    $validator1 = $request->validateFile("file");
    $validator2 = $request->validateFile("file2");

    expect([$validator1, $validator2])->toContainOnlyInstancesOf(FileValidator::class);
    expect($validator1->getAttribute()->getName())->toBe("file");
    expect($validator1->getAttribute()->getValue())->toBeInstanceOf(File::class);
});

it("should set null if key does not exist on call validateField or validateFile", function () {
    $data = [];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $validator1 = $request->validateField("name");
    $validator2 = $request->validateFile("name");

    expect($validator1->getAttribute()->getValue())->toBe(null);
    expect($validator2->getAttribute()->getValue())->toBe(null);
});

it("should set null if value is an nullable string on call validateField or validateFile", function (string $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $validator1 = $request->validateField("name");
    $validator2 = $request->validateFile("name");

    expect($validator1->getAttribute()->getValue())->toBe(null);
    expect($validator2->getAttribute()->getValue())->toBe(null);
})->with(["null", "", "undefined"]);

it("should set value to null in the original array if value is an nullable string on call validateField", function (string $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $request->validateField("name");

    expect($data["name"])->toBe(null);
})->with(["null", "", "undefined"]);

it("should set value to null in the original array if value is an nullable string on call validateFile", function (string $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $request->validateFile("name");

    expect($data["name"])->toBe(null);
})->with(["null", "", "undefined"]);

it("should set value to null in the original array if value is not sent on call validateField", function (string $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $request->validateField("email");

    expect($data["email"])->toBe(null);
})->with(["null", "", "undefined"]);

it("should set value to null in the original array if value is not sent on call validateFile", function (string $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $request->validateFile("email");

    expect($data["email"])->toBe(null);
})->with(["null", "", "undefined"]);

it("should maintain values if they are not nullables on validateField", function (mixed $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $request->validateField("name");
    expect($data["name"])->toBe($value);
})->with(["not null", "0", 0, fn () => [], false, true]);

it("should maintain values if they are not nullables on validateFile", function (mixed $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $request->validateFile("name");
    expect($data["name"])->toBe($value);
})->with(["not null", "0", 0, fn () => [], false, true]);
