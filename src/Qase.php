<?php

declare(strict_types=1);

namespace Qase\PestReporter;

/**
 * Static facade for Qase reporter
 *
 * Usage in Pest tests:
 *
 * use Qase\PestReporter\Qase;
 *
 * it('test', function () {
 *     Qase::caseId(123);
 *     Qase::title('My test');
 *     Qase::comment('Some comment');
 *     Qase::attach('/path/to/file.png');
 *
 *     expect(true)->toBeTrue();
 * });
 */
class Qase
{
    /**
     * Set Qase test case IDs for the current test
     *
     * @param int ...$ids One or more Qase test case IDs
     * @return void
     *
     * Example:
     * Qase::caseId(123);
     * Qase::caseId(1, 2, 3);
     */
    public static function caseId(int ...$ids): void
    {
        $qr = QaseReporter::getInstanceWithoutInit();
        if (!$qr) {
            return;
        }

        $qr->caseId(...$ids);
    }

    /**
     * Add comment to test case
     *
     * @param string $message
     * @return void
     *
     * Example:
     * Qase::comment("My comment");
     */
    public static function comment(string $message): void
    {
        $qr = QaseReporter::getInstanceWithoutInit();
        if (!$qr) {
            return;
        }

        $qr->comment($message);
    }

    /**
     * Add title to test case
     *
     * @param string $title
     * @return void
     *
     * Example:
     * Qase::title("My title");
     */
    public static function title(string $title): void
    {
        $qr = QaseReporter::getInstanceWithoutInit();
        if (!$qr) {
            return;
        }

        $qr->title($title);
    }

    /**
     * Set suite hierarchy for the current test
     *
     * @param string ...$suites Suite names in hierarchical order
     * @return void
     *
     * Example:
     * Qase::suite('Auth', 'Login');
     */
    public static function suite(string ...$suites): void
    {
        $qr = QaseReporter::getInstanceWithoutInit();
        if (!$qr) {
            return;
        }

        $qr->suite(...$suites);
    }

    /**
     * Set a custom field value for the current test
     *
     * @param string $name Field name
     * @param string $value Field value
     * @return void
     *
     * Example:
     * Qase::field('priority', 'high');
     */
    public static function field(string $name, string $value): void
    {
        $qr = QaseReporter::getInstanceWithoutInit();
        if (!$qr) {
            return;
        }

        $qr->field($name, $value);
    }

    /**
     * Set a parameter for the current test
     *
     * @param string $name Parameter name
     * @param string $value Parameter value
     * @return void
     *
     * Example:
     * Qase::parameter('browser', 'chrome');
     */
    public static function parameter(string $name, string $value): void
    {
        $qr = QaseReporter::getInstanceWithoutInit();
        if (!$qr) {
            return;
        }

        $qr->parameter($name, $value);
    }

    /**
     * Add attachment to test case
     *
     * @param mixed $input File path, array of file paths, or object with title/content/mime
     * @return void
     *
     * Example:
     * Qase::attach("/my_path/file.json");
     * Qase::attach(["/my_path/file.json", "/my_path/file2.json"]);
     * Qase::attach((object) ['title' => 'attachment.txt', 'content' => 'Some string', 'mime' => 'text/plain']);
     */
    public static function attach(mixed $input): void
    {
        $qr = QaseReporter::getInstanceWithoutInit();
        if (!$qr) {
            return;
        }

        $qr->attach($input);
    }
}
