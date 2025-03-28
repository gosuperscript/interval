# Interval Library

A PHP library for working with mathematical intervals. It provides an elegant way to create, compare, and work with intervals using standard mathematical notation.

## Installation

Install the package via Composer:

```bash
composer require superscript/interval
```

## Requirements

- PHP 8.3 or higher

## Usage

### Creating Intervals

You can create intervals from string notation:

```php
use Superscript\Interval\Interval;

// Create from string notation
$interval = Interval::fromString('[1,5]');  // Closed interval
$interval = Interval::fromString('(1,5)');  // Open interval 
$interval = Interval::fromString('[1,5)');  // Right-open interval
$interval = Interval::fromString('(1,5]');  // Left-open interval
```

### Interval Comparisons

Check if an interval is greater/less than a value:

```php
$interval = Interval::fromString('[2,5]');

$interval->isGreaterThan(1);      // true
$interval->isGreaterThanOrEqualTo(2);  // true
$interval->isLessThan(6);         // true
$interval->isLessThanOrEqualTo(5);     // true
```

### String Representation

Intervals can be converted back to string notation:

```php
$interval = Interval::fromString('[1,5]');
echo $interval;  // "[1,5]"
```

## Interval Notation

The library supports four types of interval notation:

- `[a,b]` - **Closed interval**: includes both endpoints `a` and `b`. Example: `[1,5]` includes all numbers from 1 to 5, including 1 and 5.
- `(a,b)` - **Open interval**: excludes both endpoints `a` and `b`. Example: `(1,5)` includes all numbers greater than 1 and less than 5, but not 1 or 5 themselves.
- `[a,b)` - **Right-open interval**: includes `a` but not `b`. Example: `[1,5)` includes 1 and all values up to (but not including) 5.
- `(a,b]` - **Left-open interval**: excludes `a` but includes `b`. Example: `(1,5]` includes all values greater than 1 up to and including 5.

The inclusion or exclusion of the endpoints determines how comparisons behave. For example, if an interval is `(1,5)`, calling `$interval->isGreaterThan(1)` will return `true` because 1 is not part of the interval. However, if the interval is `[1,5]`, then `$interval->isGreaterThanOrEqualTo(1)` will return `true` since 1 is included.

Comparisons such as `$interval->isGreaterThan($value)` evaluate whether *all* values within the interval are greater than the given value. So `[2,5]` is greater than `1` (because every number from 2 to 5 is greater than 1), but not greater than `2` unless the interval is open on the left side (e.g., `(2,5)`).

Similarly, `$interval->isLessThan($value)` checks whether all values in the interval are less than the given value. `(1,5)` is less than `6`, but not less than `5` unless the interval excludes `5` (e.g., `(1,5)` or `[1,5)`.

This logic allows for precise control over numeric comparisons, especially when you want to reason about bounds inclusivity.

## Testing

Run the test suite:

```bash
composer test
```

This includes:
- Static analysis (`composer test:types`)
- Unit tests with coverage (`composer test:unit`)
- Mutation testing (`composer test:infection`)

## License

Proprietary

## About

This package is developed and maintained by Superscript.