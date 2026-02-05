<?php

use function Qase\PestReporter\qase;

it('demonstrates fluent API with caseId', function () {
    qase()
        ->caseId(1)
        ->title('Fluent API Test')
        ->comment('This test uses fluent API');

    expect(true)->toBeTrue();
});

it('demonstrates multiple case IDs', function () {
    qase()->caseId(2, 3, 4);

    expect(1 + 1)->toBe(2);
});

it('demonstrates suite hierarchy', function () {
    qase()
        ->suite('Authentication', 'Login')
        ->field('priority', 'high')
        ->parameter('browser', 'chrome');

    expect('test')->toBeString();
});

it('demonstrates chained fluent calls', function () {
    qase()
        ->caseId(10)
        ->title('Chained Fluent API Test')
        ->suite('API', 'Fluent')
        ->field('type', 'smoke')
        ->parameter('env', 'staging')
        ->comment('Testing the fluent API chaining');

    expect(true)->toBeTrue();
});

it('demonstrates comment only', function () {
    qase()->comment('Simple comment for this test');

    expect(42)->toBe(42);
});
