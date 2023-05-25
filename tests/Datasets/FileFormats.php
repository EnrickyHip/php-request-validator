<?php

dataset("invalid_file_formats", function () {
    return [
        fn () => [
            "full_path" => "",
            "type" => "",
            "tmp_name" => "",
            "error" => 4,
            "size" => 0,
        ],
        fn () => [
            "name" => "",
            "type" => "",
            "tmp_name" => "",
            "error" => 4,
            "size" => 0,
        ],
        fn () => [
            "name" => "",
            "full_path" => "",
            "tmp_name" => "",
            "error" => 4,
            "size" => 0,
        ],
        fn () => [
            "name" => "",
            "full_path" => "",
            "type" => "",
            "error" => 4,
            "size" => 0,
        ],
        fn () => [
            "name" => "",
            "full_path" => "",
            "type" => "",
            "tmp_name" => "",
            "size" => 0,
        ],
        fn () => [
            "name" => "",
            "full_path" => "",
            "type" => "",
            "tmp_name" => "",
            "error" => 4,
        ],
    ];
});
