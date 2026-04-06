<?php

use function Qase\PestReporter\qase;

it('works with data providers', function (int $a, int $b, int $expected) {
    qase()->caseId(600);

    expect($a + $b)->toBe($expected);
})->with([
    'one plus one' => [1, 1, 2],
    'two plus two' => [2, 2, 4],
    'zero plus zero' => [0, 0, 0],
]);

it('validates email formats', function (string $email, bool $isValid) {
    qase()
        ->caseId(601)
        ->parameter('email', $email)
        ->parameter('expected', $isValid ? 'valid' : 'invalid');

    $result = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    expect($result)->toBe($isValid);
})->with([
    'valid email' => ['test@example.com', true],
    'another valid' => ['user.name@domain.org', true],
    'invalid no at' => ['invalid-email', false],
    'invalid no domain' => ['test@', false],
]);

it('calculates factorial', function (int $n, int $expected) {
    qase()->caseId(602);

    $factorial = function (int $n) use (&$factorial): int {
        return $n <= 1 ? 1 : $n * $factorial($n - 1);
    };

    expect($factorial($n))->toBe($expected);
})->with([
    [0, 1],
    [1, 1],
    [5, 120],
    [7, 5040],
]);

it('checks string length', function (string $input, int $expectedLength) {
    qase()
        ->caseId(603)
        ->comment("Testing string: '{$input}'");

    expect(strlen($input))->toBe($expectedLength);
})->with([
    ['', 0],
    ['a', 1],
    ['hello', 5],
    ['test string', 11],
]);
