<?php

use Enricky\RequestValidator\Types\FileType;
use Enricky\RequestValidator\Exceptions\InvalidExtensionException;

it("should return all image types", function () {
    $imageTypes = FileType::image();

    expect($imageTypes)->toContainOnlyInstancesOf(FileType::class);

    foreach ($imageTypes as $type) {
        expect($type->value)->toMatch("/(^image)(\/)[a-zA-Z0-9_]*/");
    }
});

it("should return all audio types", function () {
    $audioTypes = FileType::audio();

    expect($audioTypes)->toContainOnlyInstancesOf(FileType::class);

    foreach ($audioTypes as $type) {
        expect($type->value)->toMatch("/(^audio)(\/)[a-zA-Z0-9_]*/");
    }
});

it("should return all video types", function () {
    $videoTypes = FileType::video();

    expect($videoTypes)->toContainOnlyInstancesOf(FileType::class);

    foreach ($videoTypes as $type) {
        expect($type->value)->toMatch("/(^video)(\/)[a-zA-Z0-9_]*/");
    }
});

it("should return all text types", function () {
    $textTypes = FileType::text();

    expect($textTypes)->toContainOnlyInstancesOf(FileType::class);

    foreach ($textTypes as $type) {
        expect($type->value)->toMatch("/(^text)(\/)[a-zA-Z0-9_]*/");
    }
});

it("should find type by extension", function (string $extension, FileType $type) {
    expect(FileType::getFromExtension($extension))->toBe($type);
})->with([
    [".exe", FileType::EXE],
    ["exe", FileType::EXE],
    [".png", FileType::PNG],
    ["png", FileType::PNG],
    [".jpg", FileType::JPEG],
    ["jpg", FileType::JPEG],
    [".jpeg", FileType::JPEG],
    ["jpeg", FileType::JPEG],
    [".mp4", FileType::MP4],
    ["mp4", FileType::MP4],
    [".pdf", FileType::PDF],
    ["pdf", FileType::PDF],
]);

it("should throw InvalidExtensionException if extension does not exists", function (string $invalidExtension) {
    $closure = fn () => FileType::getFromExtension($invalidExtension);
    expect($closure)->toThrow(InvalidExtensionException::class);
})->with(["invalid", ".invalid", "", "..pdf", ".png.jpg", ".jpg."]);

it("should find type by extension calling tryFromExtension()", function (string $extension, FileType $type) {
    expect(FileType::tryFromExtension($extension))->toBe($type);
})->with([
    [".exe", FileType::EXE],
    ["exe", FileType::EXE],
    [".png", FileType::PNG],
    ["png", FileType::PNG],
    [".jpg", FileType::JPEG],
    ["jpg", FileType::JPEG],
    [".jpeg", FileType::JPEG],
    ["jpeg", FileType::JPEG],
    [".mp4", FileType::MP4],
    ["mp4", FileType::MP4],
    [".pdf", FileType::PDF],
    ["pdf", FileType::PDF],
]);

it("should return false if extension does not exists", function (string $invalidExtension) {
    expect(FileType::tryFromExtension($invalidExtension))->toBeFalse();
})->with(["invalid", ".invalid", "", "..pdf", ".png.jpg", ".jpg."]);
