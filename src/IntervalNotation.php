<?php

declare(strict_types=1);

namespace Superscript\Interval;

enum IntervalNotation: string
{
    case Open = '()';
    case Closed = '[]';
    case LeftOpen = '(]';
    case RightOpen = '[)';

    public function isLeftOpen(): bool
    {
        return $this === self::Open || $this === self::LeftOpen;
    }

    public function isRightOpen(): bool
    {
        return $this === self::Open || $this === self::RightOpen;
    }

    public function openingSymbol(): string
    {
        return match ($this) {
            self::Open, self::LeftOpen => '(',
            self::Closed, self::RightOpen => '[',
        };
    }

    public function closingSymbol(): string
    {
        return match ($this) {
            self::Open, self::RightOpen => ')',
            self::Closed, self::LeftOpen => ']',
        };
    }
}
