<?php

declare(strict_types=1);

use Qase\PestReporter\NullQaseReporter;

describe('NullQaseReporter', function () {

    describe('fluent API methods return $this', function () {

        it('caseId returns self', function () {
            $reporter = new NullQaseReporter();
            $result = $reporter->caseId(1, 2, 3);

            expect($result)->toBe($reporter);
        });

        it('title returns self', function () {
            $reporter = new NullQaseReporter();
            $result = $reporter->title('Test Title');

            expect($result)->toBe($reporter);
        });

        it('suite returns self', function () {
            $reporter = new NullQaseReporter();
            $result = $reporter->suite('Suite1', 'Suite2');

            expect($result)->toBe($reporter);
        });

        it('field returns self', function () {
            $reporter = new NullQaseReporter();
            $result = $reporter->field('name', 'value');

            expect($result)->toBe($reporter);
        });

        it('parameter returns self', function () {
            $reporter = new NullQaseReporter();
            $result = $reporter->parameter('name', 'value');

            expect($result)->toBe($reporter);
        });

        it('comment returns self', function () {
            $reporter = new NullQaseReporter();
            $result = $reporter->comment('Some comment');

            expect($result)->toBe($reporter);
        });

        it('attach returns self for string', function () {
            $reporter = new NullQaseReporter();
            $result = $reporter->attach('/path/to/file');

            expect($result)->toBe($reporter);
        });

        it('attach returns self for array', function () {
            $reporter = new NullQaseReporter();
            $result = $reporter->attach(['/path/to/file1', '/path/to/file2']);

            expect($result)->toBe($reporter);
        });

        it('attach returns self for object', function () {
            $reporter = new NullQaseReporter();
            $result = $reporter->attach((object)['title' => 'test', 'content' => 'data']);

            expect($result)->toBe($reporter);
        });

    });

    describe('method chaining', function () {

        it('supports full fluent chain', function () {
            $reporter = new NullQaseReporter();
            $result = $reporter
                ->caseId(1)
                ->title('Test')
                ->suite('Suite')
                ->field('priority', 'high')
                ->parameter('browser', 'chrome')
                ->comment('Comment')
                ->attach('/path/to/file');

            expect($result)->toBe($reporter);
        });

    });

});
