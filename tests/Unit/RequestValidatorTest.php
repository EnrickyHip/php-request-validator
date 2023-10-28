<?php

use Enricky\RequestValidator\Abstract\RequestValidator;
use Enricky\RequestValidator\Abstract\ValidationRule;
use Enricky\RequestValidator\Abstract\ValidatorInterface;
use Enricky\RequestValidator\ArrayValidator;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\File;
use Enricky\RequestValidator\FileValidator;

/** @param string[] $errors */
function createValidator(bool $valid, array $errors, string $name = "name", mixed $value = "value")
{
    return new class($valid, $errors, $name, $value) implements ValidatorInterface
    {
        public function __construct(
            private bool $valid,
            private array $errors,
            private string $name,
            private mixed $value
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
    $request = createRequest([], $data);

    $validator1 = $request->validateField("name");
    $validator2 = $request->validateField("email");

    expect([$validator1, $validator2])->toContainOnlyInstancesOf(FieldValidator::class);
    expect($validator1->getName())->toBe("name");
    expect($validator1->getValue())->toBe("Enricky");
});

it("should create array validators", function () {
    $data = ["numbers" => [1, 2, 3]];
    $request = createRequest([], $data);

    $validator1 = $request->validateArray("numbers");
    $validator2 = $request->validateArray("array");

    expect([$validator1, $validator2])->toContainOnlyInstancesOf(ArrayValidator::class);
    expect($validator1->getName())->toBe("numbers");
    expect($validator1->getValue())->toEqual([1, 2, 3]);
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
    expect($validator1->getName())->toBe("file");
    expect($validator1->getValue())->toBeInstanceOf(File::class);
});

it("should set null if key does not exist on call validateField, validateFile and validateArray", function () {
    $data = [];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $validator1 = $request->validateField("name");
    $validator2 = $request->validateFile("name");
    $validator3 = $request->validateArray("name");

    expect($validator1->getValue())->toBe(null);
    expect($validator2->getValue())->toBe(null);
    expect($validator3->getValue())->toBe(null);
});

it("should set null if value is an nullable string on call validateField, validateFile and validateArray", function (string $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $validator1 = $request->validateField("name");
    $validator2 = $request->validateFile("name");
    $validator3 = $request->validateArray("name");

    expect($validator1->getValue())->toBe(null);
    expect($validator2->getValue())->toBe(null);
    expect($validator3->getValue())->toBe(null);
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

it("should set value to null in the original array if value is an nullable string on call validateArray", function (string $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $request->validateArray("name");

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

it("should set value to null in the original array if value is not sent on call validateArray", function (string $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $request->validateArray("email");

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

it("should maintain values if they are not nullables on validateArray", function (mixed $value) {
    $data = ["name" => $value];
    $request = createRequest([
        createValidator(false, []),
    ], $data);

    $request->validateArray("name");
    expect($data["name"])->toBe($value);
})->with(["not null", "0", 0, fn () => [], false, true]);

test("validateFile() should add custom rule to IsFileRule", function () {
    $data = ["name" => []];
    $request = createRequest([], $data);

    $fileValidator = $request->validateFile("name", "invalid file");
    $isFileRule = $fileValidator->getRules()[0];
    expect($isFileRule->getMessage())->toBe("invalid file");
});

test("requireOr() should validate if at least one field was sent", function (mixed $value1, mixed $value2, mixed $value3) {
    $data = [
        "field1" => $value1,
        "field2" => $value2,
        "field3" => $value3,
    ];
    $request = createRequest([], $data);

    $request->requireOr(["field1", "field2", "field3"], "at least one should be send");
    expect($request->validate())->toBeTrue();
})->with([
    [null, null, "valid"],
    [null, "valid", "also_valid"],
    ["valid", false, 11],
]);

test("requireOr() should not validate if no one was sent", function () {
    $data = [];
    $request = createRequest([], $data);

    $request->requireOr(["field1", "field2", "field3"], "at least one should be send");
    expect($request->validate())->toBeFalse();
    expect($request->getErrors()[0])->toBe("at least one should be send");
});

test("requireOr() with exclusive parameter should validate if only one field was sent", function (mixed $value1, mixed $value2, mixed $value3) {
    $data = [
        "field1" => $value1,
        "field2" => $value2,
        "field3" => $value3,
    ];
    
    $request = createRequest([], $data);

    $request->requireOr(["field1", "field2", "field3"], "only one should be send", true);
    expect($request->validate())->toBeTrue();
})->with([
    [null, null, "valid"],
    [null, 11, null],
    [null, null, true],
]);

test("requireOr() with exclusive parameter should not validate if more than one field were sent", function (mixed $value1, mixed $value2, mixed $value3) {
    $data = [
        "field1" => $value1,
        "field2" => $value2,
        "field3" => $value3,
    ];

    $request = createRequest([], $data);

    $request->requireOr(["field1", "field2", "field3"], "only one should be send", true);
    expect($request->validate())->toBeFalse();
    expect($request->getErrors()[0])->toBe("only one should be send");
})->with([
    [null, 1.1, "valid"],
    [null, 11, true],
    ["valid", "not_null", 11],
]);
