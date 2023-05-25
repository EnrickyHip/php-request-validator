<?php

use Enricky\RequestValidator\Enums\FileType;
use Enricky\RequestValidator\FileValidator;
use Enricky\RequestValidator\Rules\FileTypeRule;
use Enricky\RequestValidator\Rules\IsFileRule;
use Enricky\RequestValidator\Rules\MaxFileSizeRule;

beforeEach(function () {
    $field = new AttributeMock();
    $this->fileValidator = new FileValidator($field);
});

it("should have IsFileRule by default", function () {
    expect($this->fileValidator->getRules())
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainOnlyInstancesOf(IsFileRule::class);
});

it("should add custom message to IsFileRule", function () {
    $field = new AttributeMock();
    $fileValidator = new FileValidator($field, "invalid file");
    $isFileRule = $fileValidator->getRules()[0];
    expect($isFileRule->getMessage())->toBe("invalid file");
});

it("should add FileTypeRule", function () {
    $field = new AttributeMock();
    $fileValidator = (new FileValidator($field))->type(FileType::PNG);

    expect($fileValidator->getRules())
        ->toBeArray()
        ->toHaveLength(2);

    $rule = (object)$fileValidator->getRules()[1];
    expect($rule)->toBeInstanceOf(FileTypeRule::class);
});

it("should add type rule with correct types", function (FileType|array $types) {
    $field = new AttributeMock();
    $fileValidator = (new FileValidator($field))->type($types);

    $rule = (object)$fileValidator->getRules()[1];
    if (!is_array($types)) {
        $types = [$types];
    }

    expect($rule->getTypes())->toBe($types);
})->with([
    FileType::PNG,
    [FileType::PNG, FileType::JPEG, FileType::GIF]
]);

it("should add type rule with custom message", function () {
    $field = new AttributeMock("name");
    $fileValidator = (new FileValidator($field))->type(FileType::PNG, "incorrect type");

    $rule = $fileValidator->getRules()[1];
    expect($rule->getMessage())->toBe("incorrect type");
});

test("type() should return self", function () {
    $field = new AttributeMock("name");
    $fileValidator = new FileValidator($field);

    expect($fileValidator->type(FileType::JPEG))->toBe($fileValidator);
});

it("should add MaxFileSizeRule", function () {
    $field = new AttributeMock();
    $fileValidator = (new FileValidator($field))->maxSize(1000);

    expect($fileValidator->getRules())
        ->toBeArray()
        ->toHaveLength(2);

    $rule = (object)$fileValidator->getRules()[1];
    expect($rule)->toBeInstanceOf(MaxFileSizeRule::class);
});

it("should add MaxFileSizeRule with correct sizes", function (int $size) {
    $field = new AttributeMock();
    $fileValidator = (new FileValidator($field))->maxSize($size);

    $rule = (object)$fileValidator->getRules()[1];
    expect($rule->getSize())->toBe($size);
})->with([20000, 24234, 300000, 123123, 4000, 2001]);

it("should add MaxFileSizeRule with custom message", function () {
    $field = new AttributeMock("name");
    $fileValidator = (new FileValidator($field))->maxSize(1000, "max size");

    $rule = $fileValidator->getRules()[1];
    expect($rule->getMessage())->toBe("max size");
});

test("maxSize() should return self", function () {
    $field = new AttributeMock("name");
    $fileValidator = new FileValidator($field);

    expect($fileValidator->maxSize(1000))->toBe($fileValidator);
});
