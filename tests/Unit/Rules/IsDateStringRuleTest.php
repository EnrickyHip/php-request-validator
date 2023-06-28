<?php

use Enricky\RequestValidator\Rules\IsDateStringRule;

it("should validate valid date with default format (Y-m-d)", function (string $date) {
    $isDateStringRule = new IsDateStringRule();
    expect($isDateStringRule->validate($date))->toBeTrue();
})->with([
    "2023-05-16",
    "2020-02-29",
    "2005-12-31",
    "1971-01-01",
]);

it("should not validate valid date with default format (Y-m-d)", function (string $date) {
    $isDateStringRule = new IsDateStringRule();
    expect($isDateStringRule->validate($date))->toBeFalse();
})->with([
    "20-12-2020",
    "20-03-2022",
    "2005/12/31",
    "1971/01/01",
    "15/02/2002",
    "15/02/20",
    "15/11/20",
]);

it("should validate date with different formats", function (string $format, string $date) {
    $isDateStringRule = new IsDateStringRule($format);
    expect($isDateStringRule->validate($date))->toBeTrue();
})->with([
    ["d/m/Y", "16/05/2023"],
    ["d/m/Y", "25/01/2004"],
    ["m-d-Y", "05-02-2023"],
    ["m-d-Y", "12-22-2004"],
]);

it("should not validate date with different formats", function (string $format, string $date) {
    $isDateStringRule = new IsDateStringRule($format);
    expect($isDateStringRule->validate($date))->toBeFalse();
})->with([
    ["d/m/Y", "16-05-2023"],
    ["d/m/Y", "25-01-2004"],
    ["d/m/Y", "2004-01-20"],
    ["d/m/Y", "2023-01-04"],
    ["m-d-Y", "05/02/2023"],
    ["m-d-Y", "12/22/2004"],
    ["m-d-Y", "2023-02-23"],
    ["m-d-Y", "1997-12-12"],
]);

it("should not validate date if value is not a string", function (mixed $invalidType) {
    $isDateStringRule = new IsDateStringRule();
    expect($isDateStringRule->validate($invalidType))->toBeFalse();
})->with([true, 1, fn () => [], new stdClass]);

it("should not be a major rule", function () {
    $isDateStringRule = new IsDateStringRule();
    expect($isDateStringRule->isMajor())->toBeFalse();
});

it("should return the default error message if not sent", function () {
    $isDateStringRule = new IsDateStringRule();
    expect($isDateStringRule->getMessage())->toBe("field :attributeName is not a valid date");
});

it("should return the correct error message if sent", function () {
    $isDateStringRule = new IsDateStringRule(message: "Invalid Date");
    expect($isDateStringRule->getMessage())->toBe("Invalid Date");
});

it("should get 'Y-m-d' format as default", function () {
    $isDateStringRule = new IsDateStringRule();
    expect($isDateStringRule->getFormat())->toBe("Y-m-d");
});

it("should get custom format", function (string $format) {
    $isDateStringRule = new IsDateStringRule($format);
    expect($isDateStringRule->getFormat())->toBe($format);
})->with(["d/m/Y", "m-d-Y"]);
