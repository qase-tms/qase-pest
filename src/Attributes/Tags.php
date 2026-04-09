<?php

namespace Qase\PestReporter\Attributes;

use Attribute;

/**
 * Add tags to a test case in Qase
 * Example:
 * #[Tags("smoke", "regression")]
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class Tags implements TagsAttributeInterface
{
    /** @var string[] */
    private array $tags;

    public function __construct(string ...$values)
    {
        $this->tags = $values;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
