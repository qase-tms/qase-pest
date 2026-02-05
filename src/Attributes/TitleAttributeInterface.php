<?php

namespace Qase\PestReporter\Attributes;

interface TitleAttributeInterface extends AttributeInterface
{
    public function getValue(): string;
}
