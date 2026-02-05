# Qase TMS Pest Reporter

[![License](https://img.shields.io/badge/License-Apache_2.0-blue.svg)](https://opensource.org/licenses/Apache-2.0)

A [Qase TMS](https://qase.io) reporter for [Pest PHP](https://pestphp.com/) testing framework.

## Installation

```bash
composer require --dev qase/pest-reporter
```

## Configuration

### 1. Add PHPUnit Extension

Add the Qase extension to your `phpunit.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    <extensions>
        <bootstrap class="Qase\PestReporter\QaseExtension"/>
    </extensions>
    <!-- ... -->
</phpunit>
```

### 2. Create Configuration File

Create `qase.config.json` in your project root:

```json
{
  "mode": "testops",
  "fallback": "report",
  "testops": {
    "api": {
      "token": "YOUR_QASE_API_TOKEN",
      "host": "qase.io"
    },
    "project": "YOUR_PROJECT_CODE",
    "run": {
      "title": "Pest Test Run",
      "complete": true
    }
  },
  "report": {
    "driver": "local",
    "connection": {
      "path": "./build/qase-report"
    }
  }
}
```

Or use environment variables:

```bash
QASE_MODE=testops
QASE_TESTOPS_API_TOKEN=your_token
QASE_TESTOPS_PROJECT=PROJECT_CODE
```

## Usage

### Fluent API (Recommended)

```php
use function Qase\PestReporter\qase;

it('logs in successfully', function () {
    qase()
        ->caseId(123)
        ->title('Login Test')
        ->suite('Auth', 'Login')
        ->field('priority', 'high')
        ->parameter('browser', 'chrome')
        ->comment('Testing login functionality')
        ->attach('/path/to/screenshot.png');

    // Your test code
    expect(true)->toBeTrue();
});
```

### Static Facade

```php
use Qase\PestReporter\Qase;

it('logs in successfully', function () {
    Qase::caseId(123);
    Qase::title('Login Test');
    Qase::comment('Testing login');

    expect(true)->toBeTrue();
});
```

## API Reference

### Fluent API Methods

| Method | Description |
|--------|-------------|
| `caseId(int ...$ids)` | Link test to Qase test case(s) |
| `title(string $title)` | Set custom test title |
| `suite(string ...$suites)` | Set suite hierarchy |
| `field(string $name, string $value)` | Set custom field |
| `parameter(string $name, string $value)` | Set test parameter |
| `comment(string $message)` | Add comment |
| `attach(mixed $input)` | Add attachment |

### Static Facade Methods

| Method | Description |
|--------|-------------|
| `Qase::caseId(int ...$ids)` | Link test to Qase test case(s) |
| `Qase::title(string $title)` | Set custom test title |
| `Qase::suite(string ...$suites)` | Set suite hierarchy |
| `Qase::field(string $name, string $value)` | Set custom field |
| `Qase::parameter(string $name, string $value)` | Set test parameter |
| `Qase::comment(string $message)` | Add comment |
| `Qase::attach(mixed $input)` | Add attachment |

## Attachments

### File Attachment

```php
qase()->attach('/path/to/file.png');
```

### Multiple Files

```php
qase()->attach(['/path/to/file1.png', '/path/to/file2.log']);
```

### Content Attachment

```php
qase()->attach((object)[
    'title' => 'response.json',
    'content' => json_encode($response),
    'mime' => 'application/json'
]);
```

## Data Providers

The reporter automatically extracts parameters from Pest's data providers:

```php
it('adds numbers', function (int $a, int $b, int $expected) {
    qase()->caseId(100);
    expect($a + $b)->toBe($expected);
})->with([
    'one plus one' => [1, 1, 2],
    'two plus two' => [2, 2, 4],
]);
```

## Suite Hierarchy

Use `describe()` blocks for automatic suite hierarchy, or set manually:

```php
describe('Authentication', function () {
    describe('Login', function () {
        it('logs in with valid credentials', function () {
            qase()->caseId(1)->comment('Testing login');
            expect(true)->toBeTrue();
        });
    });
});

// Or set manually:
it('test with custom suite', function () {
    qase()->suite('API', 'Users', 'Create');
    expect(true)->toBeTrue();
});
```

## Running Tests

```bash
# With local report
./vendor/bin/pest

# With Qase TMS integration
QASE_MODE=testops ./vendor/bin/pest
```

## Requirements

- PHP 8.1+
- Pest 2.0+ or 3.0+
- PHPUnit 10, 11, or 12

## License

Apache License 2.0

## Links

- [Qase TMS](https://qase.io)
- [Pest PHP](https://pestphp.com)
- [Documentation](https://docs.qase.io)
