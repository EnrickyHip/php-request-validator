<?php

use Enricky\RequestValidator\Rules\MatchRule;

it("should not be a major rule", function () {
    $matchRule = new MatchRule("", "do not match");
    expect($matchRule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    $matchRule = new MatchRule("");
    expect($matchRule->getMessage())->toBe("field :attributeName does not match the given regular expression");
});

it("should return the custom error message", function () {
    $matchRule = new MatchRule("", "do not match");
    expect($matchRule->getMessage())->toBe("do not match");
});

it("should not validat id value is not a string", function (mixed $invalidType) {
    $matchRule = new MatchRule("");
    expect($matchRule->validate($invalidType))->toBeFalse();
})->with([true, 1, fn () => [], new stdClass]);

it("should validate when the value matches the regular expression", function (string $regex, string $value) {
    $matchRule = new MatchRule($regex, "Invalid value");
    expect($matchRule->validate($value))->toBeTrue();
})->with([
    ['/^[a-z]+$/i', "abc"],
    ['/^[a-z]+$/i', "XYZ"],
    ['/^[a-z]+$/i', "AbC"],
    ['/^\d{4}-\d{2}-\d{2}$/', "2023-05-16"],
    ['/^\d{4}-\d{2}-\d{2}$/', "2022-12-31"],
    ['/^\d{4}-\d{2}-\d{2}$/', "1991-01-01"],
    ['/^[a-zA-Z0-9]+$/', "abc123"],
    ['/^[a-zA-Z0-9]+$/', "1a2b3c"],
    ['/^[a-zA-Z0-9]+$/', "123abc"],
    ['/^[\w\-.]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/i', "test@example.com"], // Phone number with optional country code
    ['/^(\+\d{1,3}\s?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/', "123-456-7890"], // URL with optional protocol and www prefix
    ['/^(https?:\/\/)?(www\.)?[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/i', "http://example.com"], // Date in the format DD/MM/YYYY
    ['/^\d{2}\/\d{2}\/\d{4}$/', "16/05/2023"], // Time in the format HH:MM:SS
    ['/^\d{2}:\d{2}:\d{2}$/', "12:34:56"], // Hexadecimal color code
    ['/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', "#FF0000"], // Social Security Number (SSN)
    ['/^\d{3}-\d{2}-\d{4}$/', "123-45-6789"],
]);

it("should not validate when the value matches the regular expression", function (string $regex, string $value) {
    $matchRule = new MatchRule($regex, "Invalid value");
    expect($matchRule->validate($value))->toBeFalse();
})->with([
    ['/^[a-z]+$/i', "123"],
    ['/^[a-z]+$/i', "ABC123"],
    ['/^[a-z]+$/i', "abc!"],
    ['/^\d{4}-\d{2}-\d{2}$/', "05-16-2023!"],
    ['/^\d{4}-\d{2}-\d{2}$/', "2023/05/16"],
    ['/^\d{4}-\d{2}-\d{2}$/', "May 16, 2023"],
    ['/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/i', "invalidemail"], // Invalid email format
    ['/^(\+\d{1,3}\s?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/', "123"], // Invalid phone number format
    ['/^(https?:\/\/)?(www\.)?[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/i', "invalidurl"], // Invalid URL format
    ['/^\d{2}\/\d{2}\/\d{4}$/', "2023/05/16"], // Invalid date format
    ['/^\d{2}:\d{2}:\d{2}$/', "12:34"], // Invalid time format
    ['/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', "#ZZZZZZ"], // Invalid hexadecimal color code
    ['/^\d{3}-\d{2}-\d{4}$/', "123-45678-9012"], // Invalid Social Security Number (SSN) format
]);
