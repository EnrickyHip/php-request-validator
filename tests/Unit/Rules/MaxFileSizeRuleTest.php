<?php

use Enricky\RequestValidator\Rules\MaxFileSizeRule;

beforeEach(function () {
    $this->rule = new MaxFileSizeRule(2000);
});

it("should not be a major rule", function () {
    expect($this->rule->isMajor())->toBeFalse();
});

it("should get max size", function (int $size) {
    $rule = new MaxFileSizeRule($size);
    expect($rule->getSize())->toBe($size);
})->with([20000, 24234, 300000, 123123, 4000, 2001]);

it("should return the default error message", function () {
    expect($this->rule->getMessage())->toBe("file :name size is bigger than maximum."); //TODO melhorar isso posteriormente. algumas strings não preciam de aspas.
});

it("should not validate if value is not a File Instance", function (mixed $value) {
    expect($this->rule->validate($value))->toBeFalse();
})->with(["value", 1, null, false, fn () => [], new stdClass()]);

it("should not validate if file size is bigger than maximium", function (int $size) {
    $file = new FileMock(size: $size);
    expect($this->rule->validate($file))->toBeFalse();
})->with([20000, 24234, 300000, 123123, 4000, 2001]);

it("should validate if file size is lower or equal the maximium", function (int $size) {
    $file = new FileMock(size: $size);
    expect($this->rule->validate($file))->toBeTrue();
})->with([2000, 123, 1, 0, 342, 234, 1999]);
