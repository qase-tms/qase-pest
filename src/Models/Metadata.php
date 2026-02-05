<?php

namespace Qase\PestReporter\Models;

class Metadata
{
    public ?string $title = null;
    public array $qaseIds = [];
    public array $suites = [];
    public array $parameters = [];
    public array $fields = [];
}
