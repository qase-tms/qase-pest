<?php

use function Qase\PestReporter\qase;

it('demonstrates fluent API with caseId', function () {
    qase()
        ->caseId(200)
        ->title('Fluent API Test')
        ->comment('This test uses fluent API');

    expect(true)->toBeTrue();
});

it('demonstrates multiple case IDs', function () {
    qase()->caseId(201, 202, 203);

    expect(1 + 1)->toBe(2);
});

it('demonstrates suite hierarchy', function () {
    qase()
        ->caseId(204)
        ->suite('Authentication', 'Login')
        ->field('priority', 'high')
        ->parameter('browser', 'chrome');

    expect('test')->toBeString();
});

it('demonstrates chained fluent calls', function () {
    qase()
        ->caseId(205)
        ->title('Chained Fluent API Test')
        ->suite('API', 'Fluent')
        ->field('type', 'smoke')
        ->parameter('env', 'staging')
        ->comment('Testing the fluent API chaining');

    expect(true)->toBeTrue();
});

it('demonstrates comment only', function () {
    qase()->caseId(206);
    qase()->comment('Simple comment for this test');

    expect(42)->toBe(42);
});
