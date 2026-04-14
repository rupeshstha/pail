<?php

test('renders entries from a custom log file', function () {
    expect('[2024-01-01 03:04:05] local.INFO: Custom file message')->toPailFile(<<<'EOF'
        ┌ 03:04:05 INFO ─────────────────────────────────┐
        │ Custom file message                            │
        └────────────────────────── : / • Auth ID: guest ┘

        EOF,
    );
});

test('renders multiple entries from a custom log file', function () {
    expect([
        '[2024-01-01 03:04:05] local.INFO: First message',
        '[2024-01-01 03:04:05] local.ERROR: Second message',
    ])->toPailFile(<<<'EOF'
        ┌ 03:04:05 INFO ─────────────────────────────────┐
        │ First message                                  │
        └────────────────────────── : / • Auth ID: guest ┘
        ┌ 03:04:05 ERROR ────────────────────────────────┐
        │ Second message                                 │
        └────────────────────────── : / • Auth ID: guest ┘

        EOF,
    );
});

test('does not warn on unparseable lines in custom file mode', function () {
    expect([
        '#0 /app/stack/trace/line.php(42): Some::method()',
        '[2024-01-01 03:04:05] local.INFO: Valid entry after trace',
    ])->toPailFile(<<<'EOF'
        ┌ 03:04:05 INFO ─────────────────────────────────┐
        │ Valid entry after trace                        │
        └────────────────────────── : / • Auth ID: guest ┘

        EOF,
    );
});
