<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/MetadataPatchDecoder.php';

use PhpJsonDecodeNullErrorLab\MetadataPatchDecoder;

function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual:   " . var_export($actual, true));
    }
}

function assertThrows(callable $operation, string $exceptionClass, string $message): void
{
    try {
        $operation();
    } catch (Throwable $error) {
        if ($error instanceof $exceptionClass) {
            return;
        }
        throw new RuntimeException($message . "\nExpected exception: {$exceptionClass}\nActual exception: " . $error::class);
    }

    throw new RuntimeException($message . "\nExpected exception: {$exceptionClass}\nActual exception: none");
}

$decoder = new MetadataPatchDecoder();
$tests = [
    'JSONのnullはメタデータ削除として扱われる' => static function () use ($decoder): void {
        assertSameValue(
            ['operation' => 'clear', 'metadata' => null],
            $decoder->decode('null'),
            '明示的なJSON nullは削除要求として扱う'
        );
    },
    'JSONオブジェクトはメタデータ置換として扱われる' => static function () use ($decoder): void {
        assertSameValue(
            ['operation' => 'replace', 'metadata' => ['locale' => 'ja-JP']],
            $decoder->decode('{"locale":"ja-JP"}'),
            'JSONオブジェクトは置換要求として扱う'
        );
    },
    '壊れたJSONを削除要求として扱ってはならない' => static function () use ($decoder): void {
        assertThrows(
            static fn () => $decoder->decode('{"locale":}'),
            \JsonException::class,
            '構文エラーのJSONは呼び出し元へ例外として伝える'
        );
    },
];

$failures = [];
foreach ($tests as $name => $test) {
    try {
        $test();
        fwrite(STDOUT, "PASS: {$name}\n");
    } catch (Throwable $error) {
        $failures[] = $name;
        fwrite(STDERR, "FAIL: {$name}\n{$error->getMessage()}\n");
    }
}

if ($failures !== []) {
    fwrite(STDERR, sprintf("%d test(s) failed.\n", count($failures)));
    exit(1);
}

fwrite(STDOUT, sprintf("%d test(s) passed.\n", count($tests)));
