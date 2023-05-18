<?php

$files = glob(__DIR__ . '/Mocks/*.php');

foreach ($files as $file) {
    require_once $file;
}
