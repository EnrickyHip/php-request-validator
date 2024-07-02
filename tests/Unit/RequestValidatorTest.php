<?php

use Enricky\RequestValidator\RequestValidator;
use Enricky\RequestValidator\ArrayValidator;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\FileValidator;

it("should validate if no rules were defined", function () {
    $data = [];
    $request = new RequestValidator($data);
    expect($request->validate())->toBeTrue();
    expect($request->getErrors())->toBeEmpty();
});

it("should add validators", function () {
    $data = [];
    $request = new RequestValidator($data);
    expect($request->getValidators())->toBeEmpty();

    $request->validateField("field");
    expect($request->getValidators())->not->toBeEmpty();
    expect($request->getValidators())->toContainOnlyInstancesOf(FieldValidator::class);
    expect($request->getValidators())->toHaveLength(1);

    $request->validateArray("array");
    expect($request->getValidators())->toHaveLength(2);
    expect($request->getValidators()[1])->toBeInstanceOf(ArrayValidator::class);

    $request->validateFile("file");
    expect($request->getValidators())->toHaveLength(3);
    expect($request->getValidators()[2])->toBeInstanceOf(FileValidator::class);
});

it("should not return duplicate errors", function () {
    $data = [];

    $request = new RequestValidator($data);
    $request->validateField("field")->isRequired("invalid1");
    $request->validateFile("file")->isRequired("invalid1");
    $request->validateArray("array")->isRequired("invalid2");

    expect($request->validate())->toBeFalse();
    expect($request->getErrors())->toEqualCanonicalizing(["invalid1", "invalid2"]);
});


it("should validate if all validators are valid with built in validators", function () {
    $data = [
        "field" => "field",
        "field2" => "field2",
        "array" => [],
    ];

    $request = new RequestValidator($data);
    $request->validateField("field")->isRequired();
    $request->validateField("field2")->isRequired();
    $request->validateArray("array")->isRequired();

    expect($request->validate())->toBeTrue();
    expect($request->getErrors())->toBeEmpty();
});


it("should not validate if at least one validator is invalid valid with built in validators", function () {
    $data = [
        "field" => "field",
        "file" => [
            "name" => "example.txt",
            "full_path" => "example.txt",
            "type" => "text/plain",
            "tmp_name" => "/tmp/phpxyz",
            "error" => 0,
            "size" => 12345
        ],
    ];

    $request = new RequestValidator($data);
    $request->validateField("field")->isRequired();
    $request->validateFile("file")->isRequired();
    $request->validateArray("array")->isRequired();

    expect($request->validate())->toBeFalse();
    expect($request->getErrors())->not->toBeEmpty();
});

it("should set null if key does not exist on call validateField, validateFile and validateArray", function () {
    $data = [];
    $request = new RequestValidator($data);

    $validator1 = $request->validateField("name");
    $validator2 = $request->validateFile("name");
    $validator3 = $request->validateArray("name");

    expect($validator1->getValue())->toBe(null);
    expect($validator2->getValue())->toBe(null);
    expect($validator3->getValue())->toBe(null);
});

it("should set null if value is an nullable string on call validateField, validateFile and validateArray", function (string $value) {
    $data = ["name" => $value];
    $request = new RequestValidator($data);

    $validator1 = $request->validateField("name");
    $validator2 = $request->validateFile("name");
    $validator3 = $request->validateArray("name");

    expect($validator1->getValue())->toBe(null);
    expect($validator2->getValue())->toBe(null);
    expect($validator3->getValue())->toBe(null);
})->with(["null", "", "undefined"]);

it("should set value to null in the original array if value is an nullable string on call validateField", function (string $value) {
    $data = ["name" => $value];
    $request = new RequestValidator($data);

    $request->validateField("name");

    expect($data["name"])->toBe(null);
})->with(["null", "", "undefined"]);

it("should set value to null in the original array if value is an nullable string on call validateFile", function (string $value) {
    $data = ["name" => $value];
    $request = new RequestValidator($data);

    $request->validateFile("name");

    expect($data["name"])->toBe(null);
})->with(["null", "", "undefined"]);

it("should set value to null in the original array if value is an nullable string on call validateArray", function (string $value) {
    $data = ["name" => $value];
    $request = new RequestValidator($data);

    $request->validateArray("name");

    expect($data["name"])->toBe(null);
})->with(["null", "", "undefined"]);

it("should set value to null in the original array if value is not sent on call validateField", function (string $value) {
    $data = ["name" => $value];
    $request = new RequestValidator($data);

    $request->validateField("email");

    expect($data["email"])->toBe(null);
})->with(["null", "", "undefined"]);

it("should set value to null in the original array if value is not sent on call validateFile", function (string $value) {
    $data = ["name" => $value];
    $request = new RequestValidator($data);

    $request->validateFile("email");

    expect($data["email"])->toBe(null);
})->with(["null", "", "undefined"]);

it("should set value to null in the original array if value is not sent on call validateArray", function (string $value) {
    $data = ["name" => $value];
    $request = new RequestValidator($data);

    $request->validateArray("email");

    expect($data["email"])->toBe(null);
})->with(["null", "", "undefined"]);

it("should maintain values if they are not nullables on validateField", function (mixed $value) {
    $data = ["name" => $value];
    $request = new RequestValidator($data);

    $request->validateField("name");
    expect($data["name"])->toBe($value);
})->with(["not null", "0", 0, fn () => [], false, true]);

it("should maintain values if they are not nullables on validateFile", function (mixed $value) {
    $data = ["name" => $value];
    $request = new RequestValidator($data);

    $request->validateFile("name");

    expect($data["name"])->toBe($value);
})->with(["not null", "0", 0, fn () => [], false, true]);

it("should maintain values if they are not nullables on validateArray", function (mixed $value) {
    $data = ["name" => $value];
    $request = new RequestValidator($data);

    $request->validateArray("name");

    expect($data["name"])->toBe($value);
})->with(["not null", "0", 0, fn () => [], false, true]);

test("validateFile() should add custom rule to IsFileRule", function () {
    $data = ["name" => []];
    $request = new RequestValidator($data);

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
    $request = new RequestValidator($data);

    $request->requireOr(["field1", "field2", "field3"], "at least one should be send");
    expect($request->validate())->toBeTrue();
})->with([
    [null, null, "valid"],
    [null, "valid", "also_valid"],
    ["valid", false, 11],
]);

test("requireOr() should not validate if no one was sent", function () {
    $data = [];
    $request = new RequestValidator($data);

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

    $request = new RequestValidator($data);

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

    $request = new RequestValidator($data);

    $request->requireOr(["field1", "field2", "field3"], "only one should be send", true);
    expect($request->validate())->toBeFalse();
    expect($request->getErrors()[0])->toBe("only one should be send");
})->with([
    [null, 1.1, "valid"],
    [null, 11, true],
    ["valid", "not_null", 11],
]);
