<?php

declare(strict_types=1);

namespace Tests\Unit;

use Qase\PestReporter\Attributes\Tags;

class TagsFixture
{
    #[Tags('smoke', 'regression')]
    public function testWithTags(): void {}

    public function testWithoutTags(): void {}
}
