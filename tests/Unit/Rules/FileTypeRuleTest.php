<?php

use Enricky\RequestValidator\Enums\FileType;
use Enricky\RequestValidator\Rules\FileTypeRule;

beforeEach(function () {
    $this->rule = new FileTypeRule([]);
});

it("should not be a major rule", function () {
    expect($this->rule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    expect($this->rule->getMessage())->toBe("file :attributeName has an invalid type.");
});

it("should validate if type is valid", function (array|FileType $types, FileType $type) {
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
]);

it("should not validate if type is invalid", function (array|FileType $types, FileType $type) {
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
]);

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
