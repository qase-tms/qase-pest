<?php

declare(strict_types=1);

namespace Qase\PestReporter;

use PHPUnit\Event\Code\TestMethod;
use Qase\PhpCommons\Interfaces\ReporterInterface;
use Qase\PhpCommons\Models\Attachment;
use Qase\PhpCommons\Models\Result;
use Qase\PhpCommons\Utils\Signature;
use Qase\PestReporter\Attributes\AttributeParserInterface;

class QaseReporter implements QaseReporterInterface
{
    private static ?QaseReporter $instance = null;
    private array $testResults = [];
    private AttributeParserInterface $attributeParser;
    private ReporterInterface $reporter;
    private ?string $currentKey = null;

    private function __construct(AttributeParserInterface $attributeParser, ReporterInterface $reporter)
    {
        $this->attributeParser = $attributeParser;
        $this->reporter = $reporter;
    }

    public static function getInstance(AttributeParserInterface $attributeParser, ReporterInterface $reporter): QaseReporter
    {
        if (!isset(self::$instance)) {
            self::$instance = new QaseReporter($attributeParser, $reporter);
        }
        return self::$instance;
    }

    public static function getInstanceWithoutInit(): ?QaseReporter
    {
        return self::$instance;
    }

    public function startTestRun(): void
    {
        $this->reporter->startRun();
    }

    public function completeTestRun(): void
    {
        $this->reporter->completeRun();
    }

    public function startTest(TestMethod $test): void
    {
        $key = $this->getTestKey($test);
        $this->currentKey = $key;

        $metadata = $this->attributeParser->parseAttribute($test->className(), $test->methodName());

        // Extract data provider parameters
        $dataProviderParams = $this->extractDataProviderParams($test);

        // Merge parameters: data provider params override static Parameter attributes
        $mergedParams = array_merge($metadata->parameters, $dataProviderParams);

        // Parse Pest method name to extract suites and title
        $parsedTest = $this->parsePestMethodName($test->methodName());

        $testResult = new Result();

        if (!empty($metadata->qaseIds)) {
            $testResult->testOpsIds = $metadata->qaseIds;
        }

        // Determine suites: user-defined > parsed from describe blocks > class namespace
        if (!empty($metadata->suites)) {
            foreach ($metadata->suites as $suite) {
                $testResult->relations->addSuite($suite);
            }
        } elseif (!empty($parsedTest['suites'])) {
            foreach ($parsedTest['suites'] as $suite) {
                $testResult->relations->addSuite($suite);
            }
        } else {
            $suites = explode('\\', $test->className());
            foreach ($suites as $suite) {
                $testResult->relations->addSuite($suite);
            }
        }

        $testResult->fields = $metadata->fields;
        $testResult->params = $mergedParams;
        $testResult->signature = $this->createSignature($test, $metadata->qaseIds, $metadata->suites, $mergedParams);
        $testResult->execution->setThread($this->getThread());

        $testResult->title = $metadata->title ?? $parsedTest['title'];

        $this->testResults[$key] = $testResult;
    }

    /**
     * Parse Pest method name to extract suites (from describe blocks) and title (from it/test)
     *
     * Example: "__pest_evaluable_Authentication__→__Login__→_it_logs_in_with_valid_credentials"
     * Returns: ['suites' => ['Authentication', 'Login'], 'title' => 'logs in with valid credentials']
     *
     * @param string $methodName
     * @return array{suites: array<string>, title: string}
     */
    private function parsePestMethodName(string $methodName): array
    {
        $suites = [];
        $title = $methodName;

        // Remove Pest's internal prefix
        $cleanName = preg_replace('/^__pest_evaluable_/', '', $methodName);

        // Check if it contains describe block separators (→ or similar patterns)
        // Pest uses patterns like "__→__" or "` → `" between describe blocks
        if (preg_match('/[→]/', $cleanName)) {
            // Split by arrow separator (with surrounding underscores/spaces)
            $parts = preg_split('/\s*[→]\s*/', str_replace('_', ' ', $cleanName));
            $parts = array_map('trim', $parts);
            $parts = array_values(array_filter($parts, fn($p) => $p !== ''));

            if (count($parts) > 1) {
                // Last part contains the test (it/test), others are suites
                $lastPart = array_pop($parts);
                $suites = $parts;

                // Extract title from the last part (remove "it " or "test " prefix)
                $title = preg_replace('/^(it|test)\s+/i', '', $lastPart);
            } else {
                $title = $this->extractSimpleTitle($cleanName);
            }
        } else {
            // Simple case without describe blocks
            $title = $this->extractSimpleTitle($cleanName);
        }

        return [
            'suites' => $suites,
            'title' => $title,
        ];
    }

    /**
     * Extract simple title from method name (no describe blocks)
     *
     * @param string $name
     * @return string
     */
    private function extractSimpleTitle(string $name): string
    {
        // Replace underscores with spaces
        $title = str_replace('_', ' ', $name);

        // Remove "it " or "test " prefix
        $title = preg_replace('/^(it|test)\s+/i', '', $title);

        // Clean up multiple spaces
        $title = preg_replace('/\s+/', ' ', $title);

        return trim($title);
    }

    public function updateStatus(TestMethod $test, string $status, ?string $message = null, ?string $stackTrace = null): void
    {
        $key = $this->getTestKey($test);

        if (!isset($this->testResults[$key])) {
            $this->startTest($test);
            $this->testResults[$key]->execution->setStatus($status);
            $this->testResults[$key]->execution->finish();

            $this->handleMessage($key, $message);
            $this->reporter->addResult($this->testResults[$key]);

            return;
        }

        $this->testResults[$key]->execution->setStatus($status);
        $this->handleMessage($key, $message);

        if ($stackTrace) {
            $this->testResults[$key]->execution->setStackTrace($stackTrace);
        }
    }

    private function handleMessage(string $key, ?string $message): void
    {
        if ($message) {
            $this->testResults[$key]->message = $this->testResults[$key]->message . "\n" . $message . "\n";
        }
    }

    public function completeTest(TestMethod $test): void
    {
        $key = $this->getTestKey($test);
        $this->testResults[$key]->execution->finish();

        $this->reporter->addResult($this->testResults[$key]);
    }

    private function getTestKey(TestMethod $test): string
    {
        $baseKey = $test->className() . '::' . $test->methodName() . ':' . $test->line();

        // Include data provider data in key to make each iteration unique
        $dataProviderParams = $this->extractDataProviderParams($test);
        if (!empty($dataProviderParams)) {
            $paramsHash = $this->generateParamsHash($dataProviderParams);
            return $baseKey . ':' . $paramsHash;
        }

        return $baseKey;
    }

    private function createSignature(TestMethod $test, ?array $ids = null, ?array $suites = null, ?array $params = null): string
    {
        $finalSuites = [];
        if ($suites) {
            $finalSuites = $suites;
        } else {
            $suites = explode('\\', $test->className());
            foreach ($suites as $suite) {
                $finalSuites[] = $suite;
            }
        }

        return Signature::generateSignature($ids, $finalSuites, $params);
    }

    private function getThread(): string
    {
        return $_ENV['TEST_TOKEN'] ?? "default";
    }

    // =====================================================
    // Fluent API methods
    // =====================================================

    /**
     * Set Qase test case IDs for the current test
     *
     * @param int ...$ids One or more Qase test case IDs
     * @return $this
     */
    public function caseId(int ...$ids): self
    {
        if (!$this->currentKey || !isset($this->testResults[$this->currentKey])) {
            return $this;
        }

        $this->testResults[$this->currentKey]->testOpsIds = array_merge(
            $this->testResults[$this->currentKey]->testOpsIds ?? [],
            $ids
        );

        return $this;
    }

    /**
     * Set custom title for the current test
     *
     * @param string $title Custom test title
     * @return $this
     */
    public function title(string $title): self
    {
        if (!$this->currentKey || !isset($this->testResults[$this->currentKey])) {
            return $this;
        }

        $this->testResults[$this->currentKey]->title = $title;

        return $this;
    }

    /**
     * Set suite hierarchy for the current test
     *
     * @param string ...$suites Suite names in hierarchical order
     * @return $this
     */
    public function suite(string ...$suites): self
    {
        if (!$this->currentKey || !isset($this->testResults[$this->currentKey])) {
            return $this;
        }

        // Add suites (replaces existing ones by creating new Relations object)
        $this->testResults[$this->currentKey]->relations = new \Qase\PhpCommons\Models\Relation();
        foreach ($suites as $suite) {
            $this->testResults[$this->currentKey]->relations->addSuite($suite);
        }

        return $this;
    }

    /**
     * Set a custom field value for the current test
     *
     * @param string $name Field name
     * @param string $value Field value
     * @return $this
     */
    public function field(string $name, string $value): self
    {
        if (!$this->currentKey || !isset($this->testResults[$this->currentKey])) {
            return $this;
        }

        $this->testResults[$this->currentKey]->fields[$name] = $value;

        return $this;
    }

    /**
     * Set a parameter for the current test
     *
     * @param string $name Parameter name
     * @param mixed $value Parameter value (will be converted to string)
     * @return $this
     */
    public function parameter(string $name, mixed $value): self
    {
        if (!$this->currentKey || !isset($this->testResults[$this->currentKey])) {
            return $this;
        }

        $this->testResults[$this->currentKey]->params[$name] = $this->convertValueToString($value);

        return $this;
    }

    /**
     * Add a comment to the current test
     *
     * @param string $message Comment message
     * @return $this
     */
    public function comment(string $message): self
    {
        if (!$this->currentKey || !isset($this->testResults[$this->currentKey])) {
            return $this;
        }

        $this->testResults[$this->currentKey]->message = $this->testResults[$this->currentKey]->message . $message . "\n";

        return $this;
    }

    /**
     * Add an attachment to the current test
     *
     * @param mixed $input File path (string), array of file paths, or object with title/content/mime
     * @return $this
     */
    public function attach(mixed $input): self
    {
        if (!$this->currentKey || !isset($this->testResults[$this->currentKey])) {
            return $this;
        }

        if (is_string($input)) {
            $attachment = $this->createAttachmentFromFile($input);
            if ($attachment !== null) {
                $this->testResults[$this->currentKey]->attachments[] = $attachment;
            }
            return $this;
        }

        if (is_array($input)) {
            foreach ($input as $item) {
                if (is_string($item)) {
                    $attachment = $this->createAttachmentFromFile($item);
                    if ($attachment !== null) {
                        $this->testResults[$this->currentKey]->attachments[] = $attachment;
                    }
                }
            }

            return $this;
        }

        if (is_object($input)) {
            $data = (array)$input;
            $this->testResults[$this->currentKey]->attachments[] = Attachment::createContentAttachment(
                $data['title'] ?? 'attachment',
                $data['content'] ?? '',
                $data['mime'] ?? null
            );
        }

        return $this;
    }

    /**
     * Create attachment by reading file content immediately
     * This ensures the file content is captured even if the file is deleted later
     */
    private function createAttachmentFromFile(string $filePath): ?Attachment
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        $fileName = basename($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        return Attachment::createContentAttachment($fileName, $content, $mimeType);
    }

    // =====================================================
    // Legacy static-style methods (for backward compatibility)
    // =====================================================

    public function addComment(string $message): void
    {
        $this->comment($message);
    }

    public function updateTitle(string $title): void
    {
        $this->title($title);
    }

    public function addAttachment(mixed $input): void
    {
        $this->attach($input);
    }

    // =====================================================
    // Data Provider extraction methods
    // =====================================================

    /**
     * Extract parameters from PHPUnit data provider
     *
     * @param TestMethod $test
     * @return array<string, string> Array of parameter name => value pairs
     */
    private function extractDataProviderParams(TestMethod $test): array
    {
        try {
            $originalData = $this->getOriginalDataProviderData($test);
            if ($originalData !== null && is_array($originalData)) {
                return $this->normalizeDataProviderData($originalData);
            }
            return [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get original data provider data by calling the data provider method directly
     * This preserves associative array structure
     *
     * @param TestMethod $test
     * @return array|null Original data provider data or null if not available
     */
    private function getOriginalDataProviderData(TestMethod $test): ?array
    {
        try {
            $dataProviderMethodName = $this->getDataProviderMethodName($test);
            if ($dataProviderMethodName === null) {
                return null;
            }

            $allDataSets = $this->invokeDataProviderMethod($test->className(), $dataProviderMethodName);
            if (!is_array($allDataSets)) {
                return null;
            }

            $dataSetName = $this->getCurrentDataSetName($test);
            if ($dataSetName === null) {
                return null;
            }

            return $this->findDataSet($allDataSets, $dataSetName);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get data provider method name from test method attributes
     */
    private function getDataProviderMethodName(TestMethod $test): ?string
    {
        $testReflection = new \ReflectionMethod($test->className(), $test->methodName());

        foreach ($testReflection->getAttributes() as $attribute) {
            $attributeName = $attribute->getName();
            if (strpos($attributeName, 'DataProvider') !== false) {
                $args = $attribute->getArguments();
                if (!empty($args) && is_string($args[0])) {
                    return $args[0];
                }
            }
        }

        return null;
    }

    /**
     * Invoke data provider method to get all data sets
     */
    private function invokeDataProviderMethod(string $className, string $methodName): ?array
    {
        $classReflection = new \ReflectionClass($className);
        if (!$classReflection->hasMethod($methodName)) {
            return null;
        }

        $method = $classReflection->getMethod($methodName);
        if (!$method->isStatic() || !$method->isPublic()) {
            return null;
        }

        $result = $method->invoke(null);
        return is_array($result) ? $result : null;
    }

    /**
     * Get current data set name from test data
     */
    private function getCurrentDataSetName(TestMethod $test): int|string|null
    {
        if (!method_exists($test, 'testData')) {
            return null;
        }

        $testDataObj = $test->testData();
        $dataProviderData = $this->getDataProviderDataFromCollection($testDataObj);

        if ($dataProviderData === null) {
            return null;
        }

        return $this->getDataSetName($dataProviderData);
    }

    /**
     * Get DataFromDataProvider object from TestDataCollection
     */
    private function getDataProviderDataFromCollection(object $testDataObj): ?object
    {
        $reflection = new \ReflectionClass($testDataObj);

        if (!$reflection->hasProperty('data')) {
            return null;
        }

        $dataProperty = $reflection->getProperty('data');
        // setAccessible not needed since PHP 8.1 - reflection properties are automatically accessible
        $dataArray = $dataProperty->getValue($testDataObj);

        return (is_array($dataArray) && !empty($dataArray)) ? $dataArray[0] : null;
    }

    /**
     * Get data set name from DataFromDataProvider object
     */
    private function getDataSetName(object $dataProviderData): int|string|null
    {
        $reflection = new \ReflectionClass($dataProviderData);

        if (!$reflection->hasMethod('dataSetName')) {
            return null;
        }

        $method = $reflection->getMethod('dataSetName');
        // setAccessible not needed since PHP 8.1

        return $method->invoke($dataProviderData);
    }

    /**
     * Find matching data set in all data sets by name or index
     */
    private function findDataSet(array $allDataSets, int|string $dataSetName): ?array
    {
        if (isset($allDataSets[$dataSetName])) {
            return $allDataSets[$dataSetName];
        }

        if (is_int($dataSetName)) {
            $allDataSetsValues = array_values($allDataSets);
            return $allDataSetsValues[$dataSetName] ?? null;
        }

        return null;
    }

    /**
     * Normalize data provider data into parameter format
     * Handles different data provider formats:
     * - Simple array: ['v1', 'v2'] -> ['param0' => 'v1', 'param1' => 'v2']
     * - Associative array: ['version' => 'v1'] -> ['version' => 'v1']
     * - Parameter pairs: ['version', 'v1'] -> ['version' => 'v1']
     *
     * @param array $data Data from data provider
     * @return array<string, string> Normalized parameters
     */
    private function normalizeDataProviderData(array $data): array
    {
        $params = [];

        // Check if indexed array with parameter pairs [name, value, ...]
        if ($this->isIndexedArray($data) && $this->looksLikeParameterPairs($data)) {
            for ($i = 0; $i < count($data); $i += 2) {
                $params[$this->convertValueToString($data[$i])] = $this->convertValueToString($data[$i + 1]);
            }
            return $params;
        }

        // Handle as regular array (indexed or associative)
        foreach ($data as $key => $value) {
            $paramName = is_numeric($key) ? 'param' . $key : $key;
            $params[$paramName] = $this->convertValueToString($value);
        }

        return $params;
    }

    /**
     * Check if array looks like parameter pairs [name, value, name, value, ...]
     */
    private function looksLikeParameterPairs(array $data): bool
    {
        if (count($data) < 2 || count($data) % 2 !== 0) {
            return false;
        }

        for ($i = 0; $i < count($data); $i += 2) {
            $nameCandidate = $data[$i];

            // Name must be a non-empty string that looks like a parameter name
            if (!is_string($nameCandidate)
                || empty($nameCandidate)
                || is_numeric($nameCandidate)
                || strlen($nameCandidate) < 3
                || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $nameCandidate)
                || $nameCandidate === ($data[$i + 1] ?? null)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if array is indexed (numeric keys starting from 0)
     */
    private function isIndexedArray(array $array): bool
    {
        if (empty($array)) {
            return false;
        }

        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * Convert any value to string for parameter representation
     *
     * @param mixed $value
     * @return string
     */
    private function convertValueToString(mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'empty';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            $stringValue = (string)$value;
            return $stringValue === '' ? 'empty' : $stringValue;
        }

        if (is_array($value)) {
            if (empty($value)) {
                return 'empty';
            }
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $stringValue = (string)$value;
                return $stringValue === '' ? 'empty' : $stringValue;
            }
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return 'empty';
    }

    /**
     * Generate a hash from parameters for use in test key
     *
     * @param array<string, string> $params
     * @return string
     */
    private function generateParamsHash(array $params): string
    {
        ksort($params);
        $paramString = http_build_query($params);
        return md5($paramString);
    }
}
