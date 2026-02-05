<?php

namespace Qase\PestReporter\Attributes;

interface SuiteAttributeInterface extends AttributeInterface
{
    public function getValue(): string;
}
