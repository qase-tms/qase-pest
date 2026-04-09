<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Qase\PestReporter\Attributes\AttributeParser;
use Qase\PestReporter\Attributes\AttributeReader;
use Qase\PestReporter\NullQaseReporter;
use Qase\PhpCommons\Loggers\Logger;

class TagsTest extends TestCase
{
    private AttributeParser $parser;

    protected function setUp(): void
    {
        $this->parser = new AttributeParser(new Logger(), new AttributeReader());
    }

    public function testParseTagsFromAttribute(): void
    {
        $metadata = $this->parser->parseAttribute(TagsFixture::class, 'testWithTags');
        $this->assertSame(['smoke', 'regression'], $metadata->tags);
    }

    public function testMergeClassAndMethodTags(): void
    {
        $metadata = $this->parser->parseAttribute(ClassTagsFixture::class, 'testWithMethodTags');
        $this->assertSame(['smoke', 'regression'], $metadata->tags);
    }

    public function testEmptyTags(): void
    {
        $metadata = $this->parser->parseAttribute(TagsFixture::class, 'testWithoutTags');
        $this->assertSame([], $metadata->tags);
    }

    public function testNullReporterTag(): void
    {
        $null = new NullQaseReporter();
        $result = $null->tag('smoke', 'regression');
        $this->assertSame($null, $result);
    }
}
