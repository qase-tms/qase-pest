<?php

use function Qase\PestReporter\qase;
use Qase\PestReporter\Qase;

it('demonstrates static facade usage', function () {
    Qase::caseId(50);
    Qase::title('Static Facade Test');
    Qase::comment('Using static facade');

    expect(true)->toBeTrue();
});

it('demonstrates mixed usage', function () {
    // Using static facade
    Qase::caseId(51);

    // Using fluent function
    qase()
        ->title('Mixed Usage Test')
        ->comment('This test uses both approaches');

    expect(1 + 1)->toBe(2);
});

it('is a simple unit test', function () {
    qase()->caseId(52)->suite('Unit', 'Math');

    $sum = array_sum([1, 2, 3, 4, 5]);
    expect($sum)->toBe(15);
});

it('tests array operations', function () {
    qase()
        ->caseId(53)
        ->suite('Unit', 'Arrays')
        ->field('complexity', 'simple');

    $arr = [3, 1, 4, 1, 5, 9, 2, 6];
    sort($arr);

    expect($arr)->toBe([1, 1, 2, 3, 4, 5, 6, 9]);
});
