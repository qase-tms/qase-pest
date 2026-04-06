<?php

use function Qase\PestReporter\qase;

it('uses single QaseId', function () {
    qase()
        ->caseId(100)
        ->title('Test with single QaseId');

    expect(true)->toBeTrue();
});

it('uses multiple QaseIds', function () {
    qase()
        ->caseId(101, 102, 103)
        ->suite('Attributes');

    expect(1)->toBeInt();
});

it('combines caseId with suite and field', function () {
    qase()
        ->caseId(104)
        ->suite('Attributes', 'Priority')
        ->field('severity', 'critical');

    expect('test')->not->toBeEmpty();
});

it('uses title and field', function () {
    qase()
        ->caseId(105)
        ->title('Custom title via fluent API')
        ->field('description', 'This is a test with custom field');

    expect([1, 2, 3])->toHaveCount(3);
});

it('uses field with layer', function () {
    qase()
        ->caseId(106)
        ->field('layer', 'e2e')
        ->field('severity', 'major');

    expect(true)->toBeTrue();
});
