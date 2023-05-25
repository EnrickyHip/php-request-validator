<?php

use Enricky\RequestValidator\Attribute;

$data = [
    ["name", "Enricky"],
    ["email", "enricky@gmail.com"],
    ["phone", null],
    ["points", 30],
    ["isLogged", true],
    ["isAdmin", false],
];

it("should return attribute name", function (string $name, mixed $value) {
    $field = new Attribute($name, $value);
    expect($field->getName())->toBe($name);
})->with($data);

it("should return attribute value", function (string $name, mixed $value) {
    $field = new Attribute($name, $value);
    expect($field->getValue())->toBe($value);
})->with($data);
