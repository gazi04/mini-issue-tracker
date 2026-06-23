<?php

use App\Enums\IssuePriority;

it('maps cases to their backing values', function (): void {
    expect(IssuePriority::Low->value)->toBe('low')
        ->and(IssuePriority::Medium->value)->toBe('medium')
        ->and(IssuePriority::High->value)->toBe('high');
});

it('renders a capitalized label', function (): void {
    expect(IssuePriority::Low->label())->toBe('Low')
        ->and(IssuePriority::Medium->label())->toBe('Medium')
        ->and(IssuePriority::High->label())->toBe('High');
});

it('exposes all backing values', function (): void {
    expect(IssuePriority::values())->toBe(['low', 'medium', 'high']);
});
