<?php

declare(strict_types=1);

use Qase\PestReporter\NullQaseReporter;

describe('NullQaseReporter', function () {

    beforeEach(function () {
        $this->reporter = new NullQaseReporter();
    });

    describe('fluent API methods return $this', function () {

        it('caseId returns self', function () {
            $result = $this->reporter->caseId(1, 2, 3);

            expect($result)->toBe($this->reporter);
        });

        it('title returns self', function () {
            $result = $this->reporter->title('Test Title');

            expect($result)->toBe($this->reporter);
        });

        it('suite returns self', function () {
            $result = $this->reporter->suite('Suite1', 'Suite2');

            expect($result)->toBe($this->reporter);
        });

        it('field returns self', function () {
            $result = $this->reporter->field('name', 'value');

            expect($result)->toBe($this->reporter);
        });

        it('parameter returns self', function () {
            $result = $this->reporter->parameter('name', 'value');

            expect($result)->toBe($this->reporter);
        });

        it('comment returns self', function () {
            $result = $this->reporter->comment('Some comment');

            expect($result)->toBe($this->reporter);
        });

        it('attach returns self for string', function () {
            $result = $this->reporter->attach('/path/to/file');

            expect($result)->toBe($this->reporter);
        });

        it('attach returns self for array', function () {
            $result = $this->reporter->attach(['/path/to/file1', '/path/to/file2']);

            expect($result)->toBe($this->reporter);
        });

        it('attach returns self for object', function () {
            $result = $this->reporter->attach((object)['title' => 'test', 'content' => 'data']);

            expect($result)->toBe($this->reporter);
        });

    });

    describe('method chaining', function () {

        it('supports full fluent chain', function () {
            $result = $this->reporter
                ->caseId(1)
                ->title('Test')
                ->suite('Suite')
                ->field('priority', 'high')
                ->parameter('browser', 'chrome')
                ->comment('Comment')
                ->attach('/path/to/file');

            expect($result)->toBe($this->reporter);
        });

    });

});
