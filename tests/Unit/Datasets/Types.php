<?php

use Enricky\RequestValidator\Enums\DataType;

dataset("correct_types", function () {
    return [
        [DataType::INT, 1],
        [DataType::INT, 10],
        [DataType::STRING, "text"],
        [DataType::STRING, "another text"],
        [DataType::BOOL, true],
        [DataType::BOOL, false],
        [DataType::FLOAT, 1.5],
        [DataType::FLOAT, 10.5],
        [DataType::FLOAT, 1],
        [DataType::FLOAT, 10],
        ["int", 1],
        ["INT", 10],
        ["string", "text"],
        ["STRING", "12345"],
        ["bool", true],
        ["BOOL", false],
        ["float", 1.5],
        ["FLOAT", 10.5],
        ["float", 1],
        ["FLOAT", 10],
    ];
});

dataset("incorrect_types", function () {
    return [
        [DataType::INT, "1"],
        [DataType::INT, true],
        [DataType::INT, []],
        [DataType::INT, 10.6],
        [DataType::STRING, 1],
        [DataType::STRING, false],
        [DataType::BOOL, "true"],
        [DataType::BOOL, 1],
        [DataType::BOOL, 0],
        [DataType::FLOAT, "adasd"],
        [DataType::FLOAT, false],
        [DataType::FLOAT, "10"],
        [DataType::FLOAT, "10.5"],
        ["int", "1"],
        ["INT", true],
        ["int", []],
        ["INT", 10.6],
        ["STRING", 1],
        ["string", false],
        ["BOOL", "true"],
        ["bool", 1],
        ["BOOL", 0],
        ["float", "adasd"],
        ["FLOAT", false],
        ["float", "10"],
        ["FLOAT", "10.5"],
    ];
});
