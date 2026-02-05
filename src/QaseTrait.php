<?php

declare(strict_types=1);

namespace Qase\PestReporter;

/**
 * Trait that provides access to Qase reporter via $this->qase()
 *
 * Add this trait to your Pest.php or use it in test files:
 *
 * uses(QaseTrait::class)->in('Feature');
 *
 * Then in your tests:
 *
 * it('test', function () {
 *     $this->qase()->caseId(123)->title('My test');
 *     expect(true)->toBeTrue();
 * });
 */
trait QaseTrait
{
    /**
     * Get the Qase reporter instance for fluent API access
     *
     * @return QaseReporter|NullQaseReporter
     */
    public function qase(): QaseReporter|NullQaseReporter
    {
        $reporter = QaseReporter::getInstanceWithoutInit();

        if ($reporter === null) {
            return new NullQaseReporter();
        }

        return $reporter;
    }
}
