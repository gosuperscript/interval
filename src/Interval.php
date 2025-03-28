<?php

declare(strict_types=1);

namespace Superscript\Interval;

use Brick\Math\BigNumber;
use Stringable;
use Webmozart\Assert\Assert;

/**
 * @phpstan-consistent-constructor
 */
class Interval implements Stringable
{
    public function __construct(
        public BigNumber $left,
        public BigNumber $right,
        public IntervalNotation $notation,
    ) {
        Assert::true($left->isLessThanOrEqualTo($right), sprintf('Left must be less than or equal to right. Got %s and %s', $left, $right));
    }

    public static function fromString(string $interval): static
    {
        preg_match(pattern: '/^(?<openingSymbol>[\[(])(?<leftEndpoint>-?\d+(\.\d+)?),\s*(?<rightEndpoint>-?\d+(\.\d+)?)?(?<closingSymbol>[])])$/', subject: $interval, matches: $matches);

        if (empty($matches)) {
            throw new \InvalidArgumentException("Invalid interval: $interval");
        }

        return new static(
            left: BigNumber::of($matches['leftEndpoint']),
            right: BigNumber::of($matches['rightEndpoint']),
            notation: IntervalNotation::from($matches['openingSymbol'] . $matches['closingSymbol']),
        );
    }

    /**
     * For an interval to be considered less than some number it
     * must be smaller than the right endpoint of the interval.
     * In other words, all parts of the interval must be smaller than the value to compare.
     */
    public function isLessThan(BigNumber|int|float $value): bool
    {
        return match ($this->notation->isRightOpen()) {
            true => $this->right->isLessThanOrEqualTo($value),
            false => $this->right->isLessThan($value),
        };
    }

    /**
     * For an interval to be considered less than or equal to some number it
     * must be less than or equal to the right endpoint of the interval.
     * In this case it does not matter if the right endpoint is open or closed.
     */
    public function isLessThanOrEqualTo(BigNumber|int|float $value): bool
    {
        return $this->right->isLessThanOrEqualTo($value);
    }

    /**
     * For an interval to be considered greater than some number it
     * must be greater than the left endpoint of the interval.
     * In other words, all part of the interval must be bigger than the value to compare.
     */
    public function isGreaterThan(BigNumber|int|float $value): bool
    {
        return match ($this->notation->isLeftOpen()) {
            true => $this->left->isGreaterThanOrEqualTo($value),
            false => $this->left->isGreaterThan($value),
        };
    }

    /**
     * For an interval to be considered greater than or equal to some number it
     * must be greater than or equal to the left endpoint of the interval.
     * In this case it does not matter if the left endpoint is open or closed.
     */
    public function isGreaterThanOrEqualTo(BigNumber|int|float $value): bool
    {
        return $this->left->isGreaterThanOrEqualTo($value);
    }

    public function __toString(): string
    {
        return "{$this->notation->openingSymbol()}{$this->left}, {$this->right}{$this->notation->closingSymbol()}";
    }
}
