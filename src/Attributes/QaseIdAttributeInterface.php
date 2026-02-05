<?php

namespace Qase\PestReporter\Attributes;

interface QaseIdAttributeInterface extends AttributeInterface
{
    public function getValue(): int;
}
