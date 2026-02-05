<?php

namespace Qase\PestReporter\Attributes;

interface QaseIdsAttributeInterface extends AttributeInterface
{
    public function getValue(): array;
}
