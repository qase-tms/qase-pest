<?php

it('passes successfully', function () {
    expect(true)->toBeTrue();
});

it('fails intentionally', function () {
    expect(false)->toBeTrue();
})->skip('Demonstration of failed test');

it('is skipped', function () {
    expect(true)->toBeTrue();
})->skip('Demonstration of skipped test');

it('performs basic math', function () {
    $result = 2 + 2;
    expect($result)->toBe(4);
});

it('works with strings', function () {
    $text = 'Hello, Qase!';
    expect($text)->toContain('Qase');
});
