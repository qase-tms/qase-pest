<?php

declare(strict_types=1);

namespace Qase\PestReporter;

/**
 * Get the Qase reporter instance for fluent API access
 *
 * Usage in Pest tests:
 *
 * use function Qase\PestReporter\qase;
 *
 * it('test', function () {
 *     qase()
 *         ->caseId(123)
 *         ->title('My test')
 *         ->comment('Some comment')
 *         ->attach('/path/to/file.png');
 *
 *     expect(true)->toBeTrue();
 * });
 *
 * @return QaseReporter|NullQaseReporter
 */
function qase(): QaseReporter|NullQaseReporter
{
    $reporter = QaseReporter::getInstanceWithoutInit();

    if ($reporter === null) {
        return new NullQaseReporter();
    }

    return $reporter;
}
