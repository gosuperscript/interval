<?php

namespace Superscript\Interval\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Superscript\Interval\Interval;
use Superscript\Interval\IntervalNotation;

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
}
