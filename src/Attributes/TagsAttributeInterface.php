<?php

namespace Qase\PestReporter\Attributes;

interface TagsAttributeInterface extends AttributeInterface
{
    /**
     * @return string[]
     */
    public function getTags(): array;
}
