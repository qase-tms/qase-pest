<?php

namespace Qase\PestReporter\Attributes;

use Attribute;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 * Set field for a test or a class
 * Example:
 * #[
 *      Field("description", "Some description"),
 *      Field("severity", "high")
 * ]
 * it('test', function () {
 *    expect(true)->toBeTrue();
 * });
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class Field implements FieldAttributeInterface
{
    private string $value;
    private string $name;

    public function __construct(string $name, string $value)
    {
        $this->value = $value;
        $this->name = $name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
