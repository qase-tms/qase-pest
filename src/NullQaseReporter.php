<?php

declare(strict_types=1);

namespace Qase\PestReporter;

/**
 * Null object pattern implementation for QaseReporter
 * Used when reporter is not initialized to prevent errors
 */
class NullQaseReporter
{
    public function caseId(int ...$ids): self
    {
        return $this;
    }

    public function title(string $title): self
    {
        return $this;
    }

    public function suite(string ...$suites): self
    {
        return $this;
    }

    public function field(string $name, string $value): self
    {
        return $this;
    }

    public function parameter(string $name, mixed $value): self
    {
        return $this;
    }

    public function comment(string $message): self
    {
        return $this;
    }

    public function attach(mixed $input): self
    {
        return $this;
    }
}
