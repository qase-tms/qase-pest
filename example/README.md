# Qase Pest Reporter Example

This is an example project demonstrating how to use the Qase Pest Reporter.

## Setup

1. Install dependencies:

```bash
composer install
```

2. Configure Qase (optional):

Edit `qase.config.json` to set your Qase API token and project code if you want to send results to Qase TMS.

## Running Tests

### Local Report Mode (default)

Run tests and generate a local report:

```bash
./vendor/bin/pest
```

The report will be saved to `./build/qase-report/`.

### TestOps Mode (send to Qase TMS)

1. Edit `qase.config.json`:
   - Change `"mode": "report"` to `"mode": "testops"`
   - Set your `QASE_TESTOPS_API_TOKEN`
   - Set your `QASE_TESTOPS_PROJECT` code

2. Or use environment variables:

```bash
QASE_MODE=testops \
QASE_TESTOPS_API_TOKEN=your_token \
QASE_TESTOPS_PROJECT=DEMO \
./vendor/bin/pest
```

## Test Examples

### Basic Tests (`tests/Feature/BasicTest.php`)

Simple tests demonstrating passed, failed, and skipped states.

### QaseId Tests (`tests/Feature/QaseIdTest.php`)

Tests demonstrating Qase metadata via fluent API:
- `qase()->caseId(123)` - Link to Qase test case
- `qase()->caseId(1, 2, 3)` - Link to multiple test cases
- `qase()->title('...')` - Custom test title
- `qase()->suite('...')` - Test suite hierarchy
- `qase()->field('name', 'value')` - Custom fields

### Fluent API (`tests/Feature/FluentApiTest.php`)

Tests using the fluent API:

```php
it('test', function () {
    qase()
        ->caseId(1)
        ->title('My Test')
        ->suite('Auth', 'Login')
        ->field('priority', 'high')
        ->parameter('browser', 'chrome')
        ->comment('Some comment');

    expect(true)->toBeTrue();
});
```

### Attachments (`tests/Feature/AttachmentsTest.php`)

Tests demonstrating file attachments:
- Attach file by path
- Attach content directly
- Attach multiple files

### Suites (`tests/Feature/SuitesTest.php`)

Tests with nested `describe()` blocks and custom suite hierarchy.

### Data Providers (`tests/Feature/DataProviderTest.php`)

Parametrized tests using Pest's `with()` syntax.

## API Usage

### 1. Global Function

```php
use function Qase\PestReporter\qase;

it('test', function () {
    qase()->caseId(123)->title('My Test');
    expect(true)->toBeTrue();
});
```

### 2. Static Facade

```php
use Qase\PestReporter\Qase;

it('test', function () {
    Qase::caseId(123);
    Qase::title('My Test');
    expect(true)->toBeTrue();
});
```
