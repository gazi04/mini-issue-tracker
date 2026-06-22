<?php

namespace App\Enums;

enum IssuePriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    /**
     * Human-readable label for display.
     */
    public function label(): string
    {
        return ucfirst($this->value);
    }

    /**
     * All backing values, useful for validation and select options.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
