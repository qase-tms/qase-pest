<?php

declare(strict_types=1);

namespace Qase\PestReporter\Events;


use PHPUnit\Event\TestRunner\Started;
use PHPUnit\Event\TestRunner\StartedSubscriber;
use Qase\PestReporter\QaseReporterInterface;

final class TestRunnerStartedSubscriber implements StartedSubscriber
{
    private QaseReporterInterface $reporter;

    public function __construct(QaseReporterInterface $reporter)
    {
        $this->reporter = $reporter;
    }

    public function notify(Started $event): void
    {
        $this->reporter->startTestRun();
    }
}
