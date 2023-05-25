<?php

use Enricky\RequestValidator\Enums\FileType;
use Enricky\RequestValidator\File;

beforeEach(function () {
    $file = [
        "name" => "example.txt",
        "full_path" => "example.txt",
        "type" => "text/plain",
        "tmp_name" => "/tmp/phpxyz",
        "error" => 0,
        "size" => 12345
    ];

    $invalidFile = [
        "name" => "",
        "full_path" => "",
        "type" => "",
        "tmp_name" => "",
        "error" => 4,
        "size" => 0,
    ];

    $this->file = new File($file);
    $this->invalidFile = new File($invalidFile);
});

it("should get all values empty and error UPLOAD_ERR_NO_FILE if invalid format", function (array $invalidFormat) {
    $this->file = new File($invalidFormat);

    expect($this->file->getName())->toBe("");
    expect($this->file->getFullPath())->toBe("");
    expect($this->file->getType())->toBeNull();
    expect($this->file->getTempName())->toBe("");
    expect($this->file->getError())->toBe(UPLOAD_ERR_NO_FILE);
    expect($this->file->getSize())->toBe(0);
})->with("invalid_file_formats");

it("should get file name", function () {
    expect($this->file->getName())->toBe("example.txt");
    expect($this->invalidFile->getName())->toBe("");
});

it("should get file path", function () {
    expect($this->file->getFullPath())->toBe("example.txt");
    expect($this->invalidFile->getFullPath())->toBe("");
});

it("should get file type", function () {
    expect($this->file->getType())->toBe(FileType::TXT);
    expect($this->invalidFile->getType())->toBeNull();
});

it("should get file temporary name", function () {
    expect($this->file->getTempName())->toBe("/tmp/phpxyz");
    expect($this->invalidFile->getTempName())->toBe("");
});

it("should get error code", function () {
    expect($this->file->getError())->toBe(UPLOAD_ERR_OK);
    expect($this->invalidFile->getError())->toBe(UPLOAD_ERR_NO_FILE);
});

it("should get file size", function () {
    expect($this->file->getSize())->toBe(12345);
    expect($this->invalidFile->getSize())->toBe(0);
});
