# Qase Pest Reporter - Usage Guide

This guide covers all available annotations, fluent API methods, and static facade for using the Qase Pest Reporter.

## Table of Contents

- [Annotations](#annotations)
  - [QaseId](#qaseid)
  - [QaseIds](#qaseids)
  - [Title](#title)
  - [Suite](#suite)
  - [Field](#field)
  - [Tags](#tags)
  - [Parameter](#parameter)
- [Fluent API](#fluent-api)
  - [qase()->caseId()](#qase-caseid)
  - [qase()->title()](#qase-title)
  - [qase()->suite()](#qase-suite)
  - [qase()->field()](#qase-field)
  - [qase()->tag()](#qase-tag)
  - [qase()->parameter()](#qase-parameter)
  - [qase()->comment()](#qase-comment)
  - [qase()->attach()](#qase-attach)
  - [qase()->step()](#qase-step)
- [Static Facade](#static-facade)
- [Examples](#examples)
- [Configuration](#configuration)
- [Running Tests](#running-tests)

## Annotations

Annotations use PHP 8 attributes and are applied directly to test methods or classes.

### QaseId

Links a test to a specific Qase test case by ID.

**Target:** Method only
**Repeatable:** No

```php
#[QaseId(123)]
it('logs in successfully', function () {
    expect(true)->toBeTrue();
});
```

### QaseIds

Links a test to multiple Qase test cases by IDs.

**Target:** Method only
**Repeatable:** No

```php
#[QaseIds([1, 2, 3])]
it('covers multiple scenarios', function () {
    expect(true)->toBeTrue();
});
```

**Note:** All IDs must be integers. Non-integer values will cause an exception.

### Title

Sets a custom title for the test case in Qase.

**Target:** Method only
**Repeatable:** No

```php
#[Title('User Login with Valid Credentials')]
it('logs in successfully', function () {
    expect(true)->toBeTrue();
});
```

### Suite

Assigns the test to one or more test suites in Qase.

**Target:** Method and Class
**Repeatable:** Yes

```php
// Single suite
#[Suite('Authentication')]
it('logs in successfully', function () {
    expect(true)->toBeTrue();
});

// Multiple suites (hierarchical)
#[
    Suite('Authentication'),
    Suite('Login')
]
it('logs in successfully', function () {
    expect(true)->toBeTrue();
});
```

### Field

Adds custom fields to the test case in Qase.

**Target:** Method and Class
**Repeatable:** Yes

```php
#[
    Field('description', 'Tests user login functionality'),
    Field('severity', 'high'),
    Field('priority', 'P1')
]
it('logs in successfully', function () {
    expect(true)->toBeTrue();
});
```

### Tags

Adds tags to the test case in Qase. Tags from class-level and method-level attributes are merged (accumulated).

**Target:** Method and Class
**Repeatable:** Yes

```php
// Single tag set
#[Tags('smoke', 'regression')]
it('logs in successfully', function () {
    expect(true)->toBeTrue();
});

// Multiple tag attributes (all tags are merged)
#[
    Tags('smoke'),
    Tags('regression', 'critical')
]
it('logs in successfully', function () {
    expect(true)->toBeTrue();
});
```

### Parameter

Adds parameters to the test case in Qase.

**Target:** Method only
**Repeatable:** Yes

```php
#[
    Parameter('browser', 'chrome'),
    Parameter('environment', 'staging')
]
it('logs in successfully', function () {
    expect(true)->toBeTrue();
});
```

## Fluent API

The fluent API is the recommended way to add metadata to Pest tests. Import the `qase()` function and chain methods:

```php
use function Qase\PestReporter\qase;
```

### qase()->caseId()

Links the test to one or more Qase test case IDs.

```php
it('logs in successfully', function () {
    qase()->caseId(123);

    expect(true)->toBeTrue();
});

// Multiple IDs
it('covers multiple cases', function () {
    qase()->caseId(1, 2, 3);

    expect(true)->toBeTrue();
});
```

### qase()->title()

Sets a custom title for the test case.

```php
it('test', function () {
    qase()->title('User Login with Valid Credentials');

    expect(true)->toBeTrue();
});
```

### qase()->suite()

Sets the suite hierarchy for the test. Replaces any suites derived from `describe()` blocks or class namespace.

```php
it('test', function () {
    qase()->suite('Authentication', 'Login');

    expect(true)->toBeTrue();
});
```

### qase()->field()

Sets a custom field value for the test case.

```php
it('test', function () {
    qase()->field('severity', 'critical');
    qase()->field('priority', 'P1');

    expect(true)->toBeTrue();
});
```

### qase()->tag()

Adds tags to the current test case. Multiple calls accumulate tags.

```php
it('test', function () {
    qase()->tag('smoke', 'regression');

    expect(true)->toBeTrue();
});

// Tags accumulate across multiple calls
it('test', function () {
    qase()->tag('smoke');
    qase()->tag('regression');

    expect(true)->toBeTrue();
});
```

### qase()->parameter()

Sets a parameter for the test case.

```php
it('test', function () {
    qase()->parameter('browser', 'chrome');
    qase()->parameter('environment', 'staging');

    expect(true)->toBeTrue();
});
```

### qase()->comment()

Adds a comment to the test case.

```php
it('test', function () {
    qase()->comment('Starting login test');

    // Test implementation

    qase()->comment('Login test completed');

    expect(true)->toBeTrue();
});
```

### qase()->attach()

Adds attachments to the test case. Supports file paths, arrays of file paths, and content objects.

```php
// Single file
it('test', function () {
    qase()->attach('/path/to/screenshot.png');

    expect(true)->toBeTrue();
});

// Multiple files
it('test', function () {
    qase()->attach(['/path/to/file1.png', '/path/to/file2.log']);

    expect(true)->toBeTrue();
});

// Content attachment
it('test', function () {
    qase()->attach((object) [
        'title' => 'response.json',
        'content' => json_encode($response),
        'mime' => 'application/json'
    ]);

    expect(true)->toBeTrue();
});
```

### qase()->step()

Adds test steps to structure test execution in Qase TMS.

```php
// Simple step markers
it('test', function () {
    qase()->step('Open login page');
    qase()->step('Enter credentials');
    qase()->step('Click submit');

    expect(true)->toBeTrue();
});

// Steps with callbacks (auto-timed, status tracked)
it('test', function () {
    qase()->step('Fill login form', function () {
        expect(true)->toBeTrue();
    });

    expect(true)->toBeTrue();
});

// Nested steps
it('test', function () {
    qase()->step('Login flow', function () {
        qase()->step('Enter credentials', function () {
            // nested step
        });
        qase()->step('Click submit');
    });

    expect(true)->toBeTrue();
});

// Steps with expected result
it('test', function () {
    qase()->step('Click login', expectedResult: 'Dashboard page loads');

    expect(true)->toBeTrue();
});
```

## Static Facade

The static facade provides the same functionality as the fluent API but using static method calls. Import the `Qase` class:

```php
use Qase\PestReporter\Qase;
```

Available methods:

| Static Method | Equivalent Fluent Method |
|---|---|
| `Qase::caseId(int ...$ids)` | `qase()->caseId(...)` |
| `Qase::title(string $title)` | `qase()->title(...)` |
| `Qase::suite(string ...$suites)` | `qase()->suite(...)` |
| `Qase::field(string $name, string $value)` | `qase()->field(...)` |
| `Qase::tag(string ...$tags)` | `qase()->tag(...)` |
| `Qase::parameter(string $name, string $value)` | `qase()->parameter(...)` |
| `Qase::comment(string $message)` | `qase()->comment(...)` |
| `Qase::attach(mixed $input)` | `qase()->attach(...)` |
| `Qase::step(string $action, ?callable $callback, ?string $expectedResult)` | `qase()->step(...)` |

Example:

```php
use Qase\PestReporter\Qase;

it('logs in successfully', function () {
    Qase::caseId(123);
    Qase::title('Login Test');
    Qase::tag('smoke', 'regression');
    Qase::comment('Testing login functionality');

    expect(true)->toBeTrue();
});
```

## Examples

### Complete Fluent API Example

```php
use function Qase\PestReporter\qase;

it('logs in successfully', function () {
    qase()
        ->caseId(123)
        ->title('Login Test')
        ->suite('Auth', 'Login')
        ->field('priority', 'high')
        ->tag('smoke', 'regression')
        ->parameter('browser', 'chrome')
        ->step('Open login page')
        ->step('Submit credentials', function () {
            expect(true)->toBeTrue();
        })
        ->comment('Testing login functionality')
        ->attach('/path/to/screenshot.png');
});
```

### Combining Annotations with Fluent API

```php
use function Qase\PestReporter\qase;
use Qase\PestReporter\Attributes\QaseId;
use Qase\PestReporter\Attributes\Tags;
use Qase\PestReporter\Attributes\Suite;

#[
    QaseId(123),
    Suite('Authentication'),
    Tags('smoke', 'regression')
]
it('logs in successfully', function () {
    qase()
        ->comment('Testing login')
        ->step('Submit form', function () {
            expect(true)->toBeTrue();
        });
});
```

### Data Providers

The reporter automatically extracts parameters from Pest's data providers:

```php
use function Qase\PestReporter\qase;

it('adds numbers', function (int $a, int $b, int $expected) {
    qase()->caseId(100);
    expect($a + $b)->toBe($expected);
})->with([
    'one plus one' => [1, 1, 2],
    'two plus two' => [2, 2, 4],
]);
```

### Suite Hierarchy with Describe Blocks

```php
use function Qase\PestReporter\qase;

describe('Authentication', function () {
    describe('Login', function () {
        it('logs in with valid credentials', function () {
            qase()
                ->caseId(1)
                ->tag('smoke')
                ->comment('Testing login');

            expect(true)->toBeTrue();
        });
    });
});
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

### 3. Environment Variables

Alternatively, use environment variables:

```bash
QASE_MODE=testops
QASE_TESTOPS_API_TOKEN=your_token
QASE_TESTOPS_PROJECT=PROJECT_CODE
```

## Running Tests

```bash
# Run with local report
./vendor/bin/pest

# Run with Qase TMS integration
QASE_MODE=testops ./vendor/bin/pest
```
