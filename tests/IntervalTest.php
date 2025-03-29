<?php

namespace Superscript\Interval\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Superscript\Interval\Interval;
use Superscript\Interval\IntervalNotation;

#[CoversClass(Interval::class)]
#[CoversClass(IntervalNotation::class)]
class IntervalTest extends TestCase
{
    #[Test]
    #[DataProvider('validCases')]
    public function it_parses_interval_from_string(string $input, int $expectedLeft, int $expectedRight, IntervalNotation $expectedNotation): void
    {
        $interval = Interval::fromString($input);
        $this->assertSame($expectedLeft, $interval->left->toInt());
        $this->assertSame($expectedRight, $interval->right->toInt());
        $this->assertSame($expectedNotation, $interval->notation);
    }

    public static function validCases(): array
    {
        return [
            ['(1,2)', 1, 2, IntervalNotation::Open],
            ['[1,2]', 1, 2, IntervalNotation::Closed],
            ['(1,2]', 1, 2, IntervalNotation::LeftOpen],
            ['[1,2)', 1, 2, IntervalNotation::RightOpen],
            ['[1,)', 1, PHP_INT_MAX, IntervalNotation::RightOpen],
            ['(,2]', PHP_INT_MIN, 2, IntervalNotation::LeftOpen],
            ['(,)', PHP_INT_MIN, PHP_INT_MAX, IntervalNotation::Open],
            ['[1,2)', 1, 2, IntervalNotation::RightOpen],
            ['(1,2]', 1, 2, IntervalNotation::LeftOpen],
        ];
    }

    #[Test]
    #[DataProvider('invalidCases')]
    public function it_throws_exception_for_invalid_interval(string $input, string $exceptionMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);
        Interval::fromString($input);
    }

    public static function invalidCases(): array
    {
        return [
            ['(1,2', 'Invalid interval: (1,2'],
            ['1,2)', 'Invalid interval: 1,2)'],
            ['1,2', 'Invalid interval: 1,2'],
            ['[1|2]', 'Invalid interval: [1|2]'],
            ['[12]', 'Invalid interval: [12]'],
            ['[[1,2)', 'Invalid interval: [[1,2)'],
            ['[1,2))', 'Invalid interval: [1,2))'],
            ['[1,]', 'Right endpoint must be defined when right side is closed.'],
            ['[,1]', 'Left endpoint must be defined when left side is closed.'],
            ['[,]', 'Left endpoint must be defined when left side is closed.']
        ];
    }

    #[Test]
    public function left_number_can_not_be_bigger_than_right_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Left must be less than or equal to right. Got 2 and 1');
        Interval::fromString('[2,1]');
    }

    #[Test]
    #[DataProvider('compareCases')]
    public function it_can_compare_intervals(string $input, string $comparator, int|float $value, bool $expectation): void
    {
        $interval = Interval::fromString($input);

        $this->assertEquals(match ($comparator) {
            '>' => $interval->isGreaterThan($value),
            '>=' => $interval->isGreaterThanOrEqualTo($value),
            '<' => $interval->isLessThan($value),
            '<=' => $interval->isLessThanOrEqualTo($value),
        }, $expectation);
    }

    public static function compareCases(): array
    {
        return [
            ['[2,5]', '>', 1, true],
            ['[2,5]', '>', 2, false],
            ['[2,5]', '>', 3, false],
            ['[2,5]', '>', 6, false],
            ['(2,5)', '>', 1, true],
            ['(2,5)', '>', 2, true],
            ['(2,5)', '>', 3, false],
            ['(2,5)', '>', 6, false],

            ['[2,5]', '>=', 1, true],
            ['[2,5]', '>=', 2, true],
            ['[2,5]', '>=', 3, false],
            ['[2,5]', '>=', 6, false],
            ['(2,5)', '>=', 1, true],
            ['(2,5)', '>=', 2, true],
            ['(2,5)', '>=', 3, false],
            ['(2,5)', '>=', 6, false],

            ['[2,5]', '<', 2, false],
            ['[2,5]', '<', 4, false],
            ['[2,5]', '<', 5, false],
            ['[2,5]', '<', 6, true],
            ['(2,5)', '<', 2, false],
            ['(2,5)', '<', 3, false],
            ['(2,5)', '<', 5, true],
            ['(2,5)', '<', 6, true],

            ['[2,5]', '<=', 2, false],
            ['[2,5]', '<=', 4, false],
            ['[2,5]', '<=', 5, true],
            ['[2,5]', '<=', 6, true],
            ['(2,5)', '<=', 2, false],
            ['(2,5)', '<=', 3, false],
            ['(2,5)', '<=', 5, true],
            ['(2,5)', '<=', 6, true],

            ['[2,)', '>', 1, true],
            ['[2,)', '>', 2, false],
            ['[2,)', '>=', 2, true],
            ['[2,)', '>=', 3, false],
            ['[2,)', '<', 2, false],
            ['[2,)', '<=', 2, false],
            ['[2,)', '<=', PHP_INT_MAX, true],

            ['(,5]', '>', 1, false],
            ['(,5]', '>', 5, false],
            ['(,5]', '>=', 1, false],
            ['(,5]', '>=', 5, false],
            ['(,5]', '>=', PHP_INT_MIN, true],
            ['(,5]', '<', 2, false],
            ['(,5]', '<=', 2, false],
            ['(,5]', '<=', 5, true],
            ['(,5]', '<', 6, true],

            ['(,)', '>', 1, false],
            ['(,)', '>=', PHP_INT_MIN, true],
            ['(,)', '<', 1, false],
            ['(,)', '<=', PHP_INT_MAX, true],
        ];
    }

    #[Test]
    #[DataProvider('stringCases')]
    public function it_can_be_transformed_to_string(string $input): void
    {
        $interval = Interval::fromString($input);
        $this->assertSame($input, (string) $interval);
    }

    public static function stringCases(): array
    {
        return [
            ['[2,5]'],
            ['(2,5)'],
            ['[2,5)'],
            ['(2,5]'],
        ];
    }
}
