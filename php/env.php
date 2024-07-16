<?php
define('ROOT_PATH_ENV', dirname(__DIR__, 1));

$path = ROOT_PATH_ENV . '/.env';

if (!file_exists($path)) {
    throw new Exception('.env file does not exist!');
}

$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lines === false) {
    throw new Exception('Unable to read the .env file!');
}

foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) {
        continue;
    }

    list($name, $value) = explode('=', $line, 2);
    $name = trim($name);
    $value = trim($value);

    if (!array_key_exists($name, $_ENV)) {
        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
}