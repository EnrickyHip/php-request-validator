<?php

use Enricky\RequestValidator\Abstract\FileInterface;
use Enricky\RequestValidator\Enums\FileType;
use Enricky\RequestValidator\Rules\IsFileRule;

beforeEach(function () {
    $this->fileRule = new IsFileRule();
});

it("should be a major rule", function () {
    expect($this->fileRule->isMajor())->toBeTrue();
});

it("should return the default error message", function () {
    expect($this->fileRule->getMessage())->toBe("field :attributeName is not a valid file");
});

it("should validate if file is valid", function () {
    $file = new FileMock(
        "test.txt",
        "test.txt",
        FileType::TXT,
        __DIR__ . "/../../test.txt",
        0,
        9
    );

    expect($this->fileRule->validate($file))->toBeTrue();
});

it("should not validate if value is not a File Instance", function (mixed $value) {
    expect($this->fileRule->validate($value))->toBeFalse();
})->with(["value", 1, null, false, fn () => [], new stdClass()]);

it("should not validate if name is empty", function () {
    $file = new FileMock(
        "",
        "test.txt",
        FileType::TXT,
        __DIR__ . "/../../test.txt",
        0,
        9
    );

    expect($this->fileRule->validate($file))->toBeFalse();
});

it("should not validate if path is empty", function () {
    $file = new FileMock(
        "test.txt",
        "",
        FileType::TXT,
        __DIR__ . "/../../test.txt",
        0,
        9
    );

    expect($this->fileRule->validate($file))->toBeFalse();
});

it("should not validate if type is null", function () {
    $file = new FileMock(
        "test.txt",
        "test.txt",
        null,
        __DIR__ . "/../../test.txt",
        0,
        9
    );

    expect($this->fileRule->validate($file))->toBeFalse();
});

it("should not validate if temp file does not exists", function () {
    $file = new FileMock(
        "test.txt",
        "test.txt",
        FileType::TXT,
        "test.txt",
        0,
        9
    );

    expect($this->fileRule->validate($file))->toBeFalse();
});

it("should not validate if code error is not UPLOAD_ERR_OK", function (int $code) {
    $file = new FileMock(
        "test.txt",
        "test.txt",
        FileType::TXT,
        __DIR__ . "/../../test.txt",
        $code,
        9
    );

    expect($this->fileRule->validate($file))->toBeFalse();
})->with([
    UPLOAD_ERR_INI_SIZE,
    UPLOAD_ERR_FORM_SIZE,
    UPLOAD_ERR_PARTIAL,
    UPLOAD_ERR_NO_FILE,
    UPLOAD_ERR_NO_TMP_DIR,
    UPLOAD_ERR_CANT_WRITE,
    UPLOAD_ERR_EXTENSION,
]);
