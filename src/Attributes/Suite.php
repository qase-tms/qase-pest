<?php

namespace Qase\PestReporter\Attributes;

use Attribute;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 * Set suite for a test or a class
 * Example:
 * #[
 *      Suite("Main suite"),
 *      Suite("Sub suite")
 * ]
 * it('test', function () {
 *    expect(true)->toBeTrue();
 * });
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class Suite implements SuiteAttributeInterface
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
