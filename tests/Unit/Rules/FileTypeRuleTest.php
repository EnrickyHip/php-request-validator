<?php

use Enricky\RequestValidator\Types\FileType;
use Enricky\RequestValidator\Exceptions\InvalidExtensionException;
use Enricky\RequestValidator\Rules\FileTypeRule;

beforeEach(function () {
    $this->rule = new FileTypeRule([]);
});

it("should not be a major rule", function () {
    expect($this->rule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    expect($this->rule->getMessage())->toBe("file :name has an invalid type.");
});

it("should validate if type is valid", function (array|string|FileType $types, FileType $type) {
    $rule = new FileTypeRule($types);
    $file = new FileMock(type: $type);
    expect($rule->validate($file))->toBeTrue();
})->with([
    [[FileType::TXT, FileType::CSV], FileType::TXT],
    [[FileType::PNG, FileType::JPEG, FileType::GIF], FileType::PNG],
    [[FileType::MP4, FileType::AVI, FileType::MPEG], FileType::MP4],
    [FileType::MP3, FileType::MP3],
    [FileType::AVI, FileType::AVI],
    [FileType::video(), FileType::AVI],
    [FileType::video(), FileType::MP4],
    [FileType::image(), FileType::JPEG],
    [FileType::image(), FileType::PNG],
    [FileType::text(), FileType::TXT],
    [FileType::text(), FileType::JS],
    [FileType::audio(), FileType::MP3],
    [FileType::audio(), FileType::AAC],
    ["mp3", FileType::MP3],
    [".mp3", FileType::MP3],
    ["avi", FileType::AVI],
    [".avi", FileType::AVI],
    ["jpg", FileType::JPEG],
    [".jpg", FileType::JPEG],
    ["jpeg", FileType::JPEG],
    [".jpeg", FileType::JPEG],
    [["txt", "avi"], FileType::AVI],
    [["txt", "avi"], FileType::TXT],
    [[".exe", ".js"], FileType::EXE],
    [[".exe", ".js"], FileType::JS],
    [[".xls", "mp4", FileType::PNG], FileType::PNG],
]);

it("should not validate if type is invalid", function (array|string|FileType $types, FileType $type) {
    $rule = new FileTypeRule($types);
    $file = new FileMock(type: $type);
    expect($rule->validate($file))->toBeFalse();
})->with([
    [[FileType::TXT, FileType::CSV], FileType::MP3],
    [[FileType::PNG, FileType::JPEG, FileType::GIF], FileType::JSON],
    [[FileType::MP4, FileType::AVI, FileType::MPEG], FileType::TXT],
    [FileType::MP3, FileType::AVI],
    [FileType::AVI, FileType::MP3],
    [FileType::video(), FileType::MP3],
    [FileType::video(), FileType::PNG],
    [FileType::image(), FileType::AVI],
    [FileType::image(), FileType::TXT],
    [FileType::text(), FileType::AAC],
    [FileType::text(), FileType::MP4],
    [FileType::audio(), FileType::HTML],
    [FileType::audio(), FileType::JS],
    ["mp3", FileType::AVI],
    [".mp3", FileType::PNG],
    ["avi", FileType::JS],
    [".avi", FileType::TXT],
    ["jpg", FileType::PNG],
    [".jpg", FileType::PNG],
    ["jpeg", FileType::PNG],
    [".jpeg", FileType::PNG],
    [["txt", "avi"], FileType::JPEG],
    [["txt", "avi"], FileType::TS],
    [[".exe", ".js"], FileType::PHP],
    [[".exe", ".js"], FileType::CSS],
    [[".xls", "mp4", FileType::PNG], FileType::HTML],
]);

it("should throw InvalidExtensionException if extension does not exists", function (array|string $types) {
    $closure = fn () => new FileTypeRule($types);
    expect($closure)->toThrow(InvalidExtensionException::class);
})->with(["", ".ext", "invalid", ".invalid", fn () => [".txt", "js", ".invalid"]]);

it("should not validate if value is not a File Instance", function (mixed $value) {
    expect($this->rule->validate($value))->toBeFalse();
})->with(["value", 1, null, false, fn () => [], new stdClass()]);

it("should not validate if type is null", function () {
    $rule = new FileTypeRule([]);
    $file = new FileMock();
    expect($rule->validate($file))->toBeFalse();
});

// it("should resolve allowed types with one type", function () {
//     $rule = new FileTypeRule(FileType::TXT);
//     $attribute = new AttributeMock();
//     expect($rule->resolveMessage($attribute))->toBe("file 'name' has an invalid type. Allowed types: ['text/plain']");
// });

// it("should resolve allowed types with many types", function () {
//     $rule = new FileTypeRule([FileType::PNG, FileType::JPEG, FileType::GIF]);
//     $attribute = new AttributeMock();
//     expect($rule->resolveMessage($attribute))->toBe("file 'name' has an invalid type. Allowed types: ['iamge/png', 'iamge/jpeg', 'image/gif']");
// });
