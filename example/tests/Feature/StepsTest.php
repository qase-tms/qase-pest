<?php

use Qase\PestReporter\Qase;
use function Qase\PestReporter\qase;

it('demonstrates simple step markers', function () {
    qase()->caseId(300);
    qase()->step('Open login page');
    qase()->step('Enter username');
    qase()->step('Enter password');
    qase()->step('Click login');

    expect(true)->toBeTrue();
});

it('demonstrates steps with callbacks', function () {
    qase()->caseId(301);
    qase()->step('Open login page', function () {
        expect(true)->toBeTrue();
    });

    qase()->step('Submit form', function () {
        expect(true)->toBeTrue();
    });
});

it('demonstrates nested steps', function () {
    qase()->caseId(302);
    qase()->step('Login flow', function () {
        qase()->step('Enter credentials', function () {
            expect(true)->toBeTrue();
        });
        qase()->step('Click submit');
    });

    qase()->step('Verify dashboard', function () {
        qase()->step('Check welcome message');
        qase()->step('Check navigation menu');
    });
});

it('demonstrates steps with expected results', function () {
    qase()->caseId(303);
    qase()->step('Click login button', expectedResult: 'Dashboard page loads');
    qase()->step('Open settings', function () {
        expect(true)->toBeTrue();
    }, expectedResult: 'Settings page is displayed');
});

it('demonstrates facade steps', function () {
    qase()->caseId(304);
    Qase::step('Open page', function () {
        expect(true)->toBeTrue();
    });

    Qase::step('Verify content');
    Qase::step('Check footer', expectedResult: 'Footer is visible');
});

it('demonstrates steps mixed with fluent API', function () {
    qase()
        ->caseId(305)
        ->title('Steps with fluent API')
        ->suite('Integration', 'Steps')
        ->step('Open page')
        ->step('Fill form', function () {
            expect(true)->toBeTrue();
        })
        ->step('Submit', expectedResult: 'Form submitted')
        ->comment('Test completed with steps');

    expect(true)->toBeTrue();
});
