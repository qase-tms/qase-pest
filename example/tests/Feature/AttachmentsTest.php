<?php

use function Qase\PestReporter\qase;

it('attaches a file', function () {
    $tempFile = sys_get_temp_dir() . '/test-attachment.txt';
    file_put_contents($tempFile, 'Test content for attachment');

    qase()
        ->caseId(400)
        ->attach($tempFile)
        ->comment('File attached to this test');

    expect(file_exists($tempFile))->toBeTrue();

    unlink($tempFile);
});

it('attaches content directly', function () {
    qase()
        ->caseId(401)
        ->attach((object)[
            'title' => 'response.json',
            'content' => json_encode(['status' => 'ok', 'message' => 'Success']),
            'mime' => 'application/json'
        ])
        ->comment('JSON content attached');

    expect(true)->toBeTrue();
});

it('attaches multiple files', function () {
    $tempFile1 = sys_get_temp_dir() . '/attachment1.txt';
    $tempFile2 = sys_get_temp_dir() . '/attachment2.txt';

    file_put_contents($tempFile1, 'Content 1');
    file_put_contents($tempFile2, 'Content 2');

    qase()
        ->caseId(402)
        ->attach([$tempFile1, $tempFile2])
        ->comment('Multiple files attached');

    expect(file_exists($tempFile1))->toBeTrue();
    expect(file_exists($tempFile2))->toBeTrue();

    unlink($tempFile1);
    unlink($tempFile2);
});

it('attaches text content', function () {
    qase()
        ->caseId(403)
        ->attach((object)[
            'title' => 'debug.log',
            'content' => "Line 1: Starting test\nLine 2: Test in progress\nLine 3: Test completed",
            'mime' => 'text/plain'
        ]);

    expect('attachment')->toBeString();
});
