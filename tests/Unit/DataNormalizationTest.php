<?php

declare(strict_types=1);

use Qase\PestReporter\QaseReporter;

/**
 * Testable wrapper to access private methods of QaseReporter
 */
class TestableQaseReporter
{
    private \ReflectionClass $reflection;
    private object $instance;

    public function __construct()
    {
        $this->reflection = new \ReflectionClass(QaseReporter::class);

        // Create instance without constructor using reflection
        $this->instance = $this->reflection->newInstanceWithoutConstructor();
    }

    public function normalizeDataProviderData(array $data): array
    {
        return $this->invokePrivateMethod('normalizeDataProviderData', [$data]);
    }

    public function looksLikeParameterPairs(array $data): bool
    {
        return $this->invokePrivateMethod('looksLikeParameterPairs', [$data]);
    }

    public function isIndexedArray(array $array): bool
    {
        return $this->invokePrivateMethod('isIndexedArray', [$array]);
    }

    public function convertValueToString(mixed $value): string
    {
        return $this->invokePrivateMethod('convertValueToString', [$value]);
    }

    public function generateParamsHash(array $params): string
    {
        return $this->invokePrivateMethod('generateParamsHash', [$params]);
    }

    private function invokePrivateMethod(string $methodName, array $args): mixed
    {
        $method = $this->reflection->getMethod($methodName);
        return $method->invoke($this->instance, ...$args);
    }
}

// Helper function to create reporter instance
function reporter(): TestableQaseReporter
{
    return new TestableQaseReporter();
}

describe('Data Normalization', function () {

    describe('isIndexedArray', function () {

        it('returns true for sequential numeric keys', function () {
            expect(reporter()->isIndexedArray([1, 2, 3]))->toBeTrue();
            expect(reporter()->isIndexedArray(['a', 'b', 'c']))->toBeTrue();
        });

        it('returns false for associative arrays', function () {
            expect(reporter()->isIndexedArray(['key' => 'value']))->toBeFalse();
            expect(reporter()->isIndexedArray(['a' => 1, 'b' => 2]))->toBeFalse();
        });

        it('returns false for empty array', function () {
            expect(reporter()->isIndexedArray([]))->toBeFalse();
        });

        it('returns false for non-sequential numeric keys', function () {
            expect(reporter()->isIndexedArray([0 => 'a', 2 => 'b']))->toBeFalse();
        });

    });

    describe('looksLikeParameterPairs', function () {

        it('returns true for valid parameter pairs', function () {
            expect(reporter()->looksLikeParameterPairs(['browser', 'chrome', 'version', '1.0']))->toBeTrue();
        });

        it('returns false for odd number of elements', function () {
            expect(reporter()->looksLikeParameterPairs(['browser', 'chrome', 'version']))->toBeFalse();
        });

        it('returns false for numeric parameter names', function () {
            expect(reporter()->looksLikeParameterPairs(['123', 'value']))->toBeFalse();
        });

        it('returns false for short parameter names', function () {
            expect(reporter()->looksLikeParameterPairs(['ab', 'value']))->toBeFalse();
        });

        it('returns false for empty array', function () {
            expect(reporter()->looksLikeParameterPairs([]))->toBeFalse();
        });

        it('returns false when name equals value', function () {
            expect(reporter()->looksLikeParameterPairs(['test', 'test']))->toBeFalse();
        });

    });

    describe('convertValueToString', function () {

        it('converts string as is', function () {
            expect(reporter()->convertValueToString('hello'))->toBe('hello');
        });

        it('converts integer to string', function () {
            expect(reporter()->convertValueToString(42))->toBe('42');
        });

        it('converts float to string', function () {
            expect(reporter()->convertValueToString(3.14))->toBe('3.14');
        });

        it('converts boolean true to string', function () {
            expect(reporter()->convertValueToString(true))->toBe('true');
        });

        it('converts boolean false to string', function () {
            expect(reporter()->convertValueToString(false))->toBe('false');
        });

        it('converts array to JSON', function () {
            expect(reporter()->convertValueToString(['a', 'b']))->toBe('["a","b"]');
        });

        it('converts associative array to JSON', function () {
            expect(reporter()->convertValueToString(['key' => 'value']))->toBe('{"key":"value"}');
        });

        it('converts null to "empty"', function () {
            expect(reporter()->convertValueToString(null))->toBe('empty');
        });

        it('converts empty string to "empty"', function () {
            expect(reporter()->convertValueToString(''))->toBe('empty');
        });

    });

    describe('normalizeDataProviderData', function () {

        it('converts indexed array to param0, param1 format', function () {
            // Using numeric values to avoid being interpreted as parameter pairs
            $result = reporter()->normalizeDataProviderData([1, 2, 3]);

            expect($result)->toBe(['param0' => '1', 'param1' => '2', 'param2' => '3']);
        });

        it('preserves associative array keys', function () {
            $result = reporter()->normalizeDataProviderData(['browser' => 'chrome', 'version' => '1.0']);

            expect($result)->toBe(['browser' => 'chrome', 'version' => '1.0']);
        });

        it('converts parameter pairs to associative array', function () {
            $result = reporter()->normalizeDataProviderData(['browser', 'chrome', 'version', '1.0']);

            expect($result)->toBe(['browser' => 'chrome', 'version' => '1.0']);
        });

        it('converts nested arrays to JSON', function () {
            $result = reporter()->normalizeDataProviderData(['data' => ['nested' => 'value']]);

            expect($result['data'])->toBe('{"nested":"value"}');
        });

    });

    describe('generateParamsHash', function () {

        it('generates consistent hash for same params', function () {
            $r = reporter();
            $hash1 = $r->generateParamsHash(['a' => '1', 'b' => '2']);
            $hash2 = $r->generateParamsHash(['a' => '1', 'b' => '2']);

            expect($hash1)->toBe($hash2);
        });

        it('generates same hash regardless of key order', function () {
            $r = reporter();
            $hash1 = $r->generateParamsHash(['a' => '1', 'b' => '2']);
            $hash2 = $r->generateParamsHash(['b' => '2', 'a' => '1']);

            expect($hash1)->toBe($hash2);
        });

        it('generates different hash for different params', function () {
            $r = reporter();
            $hash1 = $r->generateParamsHash(['a' => '1']);
            $hash2 = $r->generateParamsHash(['a' => '2']);

            expect($hash1)->not->toBe($hash2);
        });

        it('returns valid md5 hash', function () {
            $hash = reporter()->generateParamsHash(['key' => 'value']);

            expect($hash)->toMatch('/^[a-f0-9]{32}$/');
        });

    });

});
