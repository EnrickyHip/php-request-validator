<?php

use Enricky\RequestValidator\Attribute;

$data = [
    "name" => "Enricky",
    "email" => "enricky@gmail.com",
    "phone" => null,
    "points" => 30,
    "isLogged" => true,
    "isAdmin" => false,
];

it("should return field name", function (string $name) use ($data) {
    $field = new Attribute($data, $name);
    expect($field->getName())->toBe($name);
})->with(array_keys($data));

it("should return field value", function (string $name) use ($data) {
    $field = new Attribute($data, $name);
    expect($field->getValue())->toBe($data[$name]);
})->with(array_keys($data));

it("should set value as null if not sent", function ($name) use ($data) {
    $field = new Attribute($data, $name);
    expect($field->getValue())->toBeNull();
})->with(["password", "", "key"]);

it("should set value as null if empty string sent", function () {
    $data = ["value" => ""];
    $field = new Attribute($data, "value");
    expect($field->getValue())->toBeNull();
});
