<?php

use Enricky\RequestValidator\Rules\IsEmailRule;

beforeEach(function () {
    $this->emailRule = new IsEmailRule();
});

it("should validate", function (string $email) {
    expect($this->emailRule->validate($email))->toBeTrue();
})->with([
    "test@example.com",
    "john.doe@example.com",
    "jane_doe@example.com",
    "user123@example.com",
    "info@example.com",
    "support@example.com",
    "contact@example.com",
    "admin@example.com",
    "no-reply@example.com",
    "first.last@example.com",
]);

it("should not validate", function (string $email) {
    expect($this->emailRule->validate($email))->toBeFalse();
})->with([
    "invalid_email@example",
    "missingat.com",
    "@domain.com",
    "email@",
    "email@domain",
    "email.domain.com",
    "email@domain..com",
    "email@-domain.com",
    "email@domain-.com",
    "email@123.123.123.123",
]);

it("should not be a major rule", function () {
    expect($this->emailRule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    expect($this->emailRule->getMessage())->toBe("field :name is not a valid email address");
});

it("should return the custom error message", function () {
    $emailRule = new IsEmailRule("invalid email");
    expect($emailRule->getMessage())->toBe("invalid email");
});
