<?php

namespace Qase\PestReporter\Attributes;

use Attribute;

/**
 * @Annotation
 * @Target({"METHOD"})
 * Set Qase ID for a test
 * Example:
 * #[QaseId(123)]
 * it('test', function () {
 *    expect(true)->toBeTrue();
 * });
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class QaseId implements QaseIdAttributeInterface
{
    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
