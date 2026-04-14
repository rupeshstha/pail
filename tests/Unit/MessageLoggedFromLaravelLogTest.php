<?php

use Laravel\Pail\ValueObjects\MessageLogged;

beforeEach(function () {
    $_ENV['PAIL_TESTS'] = true;
});

afterEach(function () {
    unset($_ENV['PAIL_TESTS']);
});

test('parses all standard log levels', function (string $level) {
    $line = "[2024-01-01 03:04:05] local.{$level}: Test message";

    $message = MessageLogged::fromLaravelLog($line);

    expect($message->level())->toBe($level)
        ->and($message->message())->toBe('Test message');
})->with(['INFO', 'ERROR', 'DEBUG', 'WARNING', 'NOTICE', 'CRITICAL', 'ALERT', 'EMERGENCY']);

test('parses message correctly', function () {
    $message = MessageLogged::fromLaravelLog('[2024-01-01 03:04:05] local.INFO: Custom file message');

    expect($message->message())->toBe('Custom file message');
});

test('preserves json context in message', function () {
    $line = '[2024-01-01 03:04:05] local.INFO: SELECT * FROM users WHERE id = 1 {"time":1.25,"bindings":[]} []';

    $message = MessageLogged::fromLaravelLog($line);

    expect($message->message())->toBe('SELECT * FROM users WHERE id = 1 {"time":1.25,"bindings":[]} []');
});

test('parses different channels', function () {
    $message = MessageLogged::fromLaravelLog('[2024-01-01 03:04:05] production.INFO: Production message');

    expect($message->level())->toBe('INFO')
        ->and($message->message())->toBe('Production message');
});

test('has no auth id', function () {
    $message = MessageLogged::fromLaravelLog('[2024-01-01 03:04:05] local.INFO: Test message');

    expect($message->authId())->toBeNull();
});

test('returns correct time', function () {
    $message = MessageLogged::fromLaravelLog('[2024-01-01 03:04:05] local.INFO: Test message');

    expect($message->time())->toBe('03:04:05');
});

test('returns correct date', function () {
    $message = MessageLogged::fromLaravelLog('[2024-01-01 03:04:05] local.INFO: Test message');

    expect($message->date())->toBe('2024-01-01 03:04:05');
});

test('throws on unparseable lines', function (string $line) {
    MessageLogged::fromLaravelLog($line);
})->with([
    'plain text' => ['not a valid log line'],
    'stack trace line' => ['#0 /app/Http/Controllers/HomeController.php(42): SomeClass->method()'],
    'empty string' => [''],
    'json only' => ['{"message":"test","level":"info"}'],
])->throws(InvalidArgumentException::class);
