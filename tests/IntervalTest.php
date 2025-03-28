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
        ];
    }

    #[Test]
    #[DataProvider('invalidCases')]
    public function it_throws_exception_for_valid_interval(string $input, string $exceptionMessage): void
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
        ];
    }

    #[Test]
    #[DataProvider('compareCases')]
    public function it_can_compare_intervals(string $input, string $comparator, int $value, bool $expectation): void
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
