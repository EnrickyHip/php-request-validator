<?php

use Enricky\RequestValidator\Rules\IsUrlRule;

beforeEach(function () {
    $this->urlRule = new IsUrlRule();
});

it("should validate", function (string $url) {
    expect($this->urlRule->validate($url))->toBeTrue();
})->with([
    "http://www.example.com",
    "https://www.example.com",
    "https://example.com",
    "http://subdomain.example.com",
    "http://example.com/page",
    "http://example.com/page?id=123",
    "http://example.com/path/to/page",
    "http://example.com/path/to/page?param=value",
    "http://example.com#section",
    "http://example.com/path/to/page#section",
]);

it("should not validate", function (string $url) {
    expect($this->urlRule->validate($url))->toBeFalse();
})->with([
    "example.com",
    "htp://example.com",
    "http://",
    "http://example",
    "http://example.",
    "http://.com",
    "http://example.com/path with spaces",
    "http://example.com?query=invalid value",
    "http://example.com#invalid section",
    "http://[::1]",
]);

it("should not validat id value is not a string", function (mixed $invalidType) {
    expect($this->urlRule->validate($invalidType))->toBeFalse();
})->with([true, 1, fn () => [], new stdClass]);

it("should not be a major rule", function () {
    expect($this->urlRule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    expect($this->urlRule->getMessage())->toBe("field :attributeName is not a valid url");
});

it("should return the custom error message", function () {
    $urlRule = new IsUrlRule("invalid url");
    expect($urlRule->getMessage())->toBe("invalid url");
});
