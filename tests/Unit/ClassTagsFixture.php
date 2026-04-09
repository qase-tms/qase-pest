<?php

declare(strict_types=1);

namespace Tests\Unit;

use Qase\PestReporter\Attributes\Tags;

#[Tags('smoke')]
class ClassTagsFixture
{
    #[Tags('regression')]
    public function testWithMethodTags(): void {}
}
