<?php

use function Qase\PestReporter\qase;

it('passes successfully', function () {
    qase()->caseId(1);
    expect(true)->toBeTrue();
});

it('fails intentionally', function () {
    qase()->caseId(2);
    expect(false)->toBeTrue();
});

it('is skipped', function () {
    qase()->caseId(3);
    expect(true)->toBeTrue();
})->skip('Demonstration of skipped test');

it('performs basic math', function () {
    qase()->caseId(4);
    $result = 2 + 2;
    expect($result)->toBe(4);
});

it('works with strings', function () {
    qase()->caseId(5);
    $text = 'Hello, Qase!';
    expect($text)->toContain('Qase');
});
