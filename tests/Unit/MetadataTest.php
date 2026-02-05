<?php

declare(strict_types=1);

use Qase\PestReporter\Models\Metadata;

describe('Metadata', function () {

    it('initializes with null title', function () {
        $metadata = new Metadata();

        expect($metadata->title)->toBeNull();
    });

    it('initializes with empty qaseIds array', function () {
        $metadata = new Metadata();

        expect($metadata->qaseIds)->toBe([]);
    });

    it('initializes with empty suites array', function () {
        $metadata = new Metadata();

        expect($metadata->suites)->toBe([]);
    });

    it('initializes with empty parameters array', function () {
        $metadata = new Metadata();

        expect($metadata->parameters)->toBe([]);
    });

    it('initializes with empty fields array', function () {
        $metadata = new Metadata();

        expect($metadata->fields)->toBe([]);
    });

    it('allows setting title', function () {
        $metadata = new Metadata();
        $metadata->title = 'Test Title';

        expect($metadata->title)->toBe('Test Title');
    });

    it('allows setting qaseIds', function () {
        $metadata = new Metadata();
        $metadata->qaseIds = [1, 2, 3];

        expect($metadata->qaseIds)->toBe([1, 2, 3]);
    });

    it('allows setting suites', function () {
        $metadata = new Metadata();
        $metadata->suites = ['Suite1', 'Suite2'];

        expect($metadata->suites)->toBe(['Suite1', 'Suite2']);
    });

    it('allows setting parameters', function () {
        $metadata = new Metadata();
        $metadata->parameters = ['browser' => 'chrome'];

        expect($metadata->parameters)->toBe(['browser' => 'chrome']);
    });

    it('allows setting fields', function () {
        $metadata = new Metadata();
        $metadata->fields = ['priority' => 'high'];

        expect($metadata->fields)->toBe(['priority' => 'high']);
    });

});
