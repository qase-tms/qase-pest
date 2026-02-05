<?php

namespace Qase\PestReporter\Attributes;

interface FieldAttributeInterface extends AttributeInterface
{
    public function getName(): string;

    public function getValue(): string;
}
