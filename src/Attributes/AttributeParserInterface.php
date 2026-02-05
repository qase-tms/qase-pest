<?php

namespace Qase\PestReporter\Attributes;

use Qase\PestReporter\Models\Metadata;

interface AttributeParserInterface
{
    public function parseAttribute(string $className, string $methodName): Metadata;
}
