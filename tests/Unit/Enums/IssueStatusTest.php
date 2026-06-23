<?php

use App\Enums\IssueStatus;

it('maps cases to their backing values', function (): void {
    expect(IssueStatus::Open->value)->toBe('open')
        ->and(IssueStatus::InProgress->value)->toBe('in_progress')
        ->and(IssueStatus::Closed->value)->toBe('closed');
});

it('renders a human-readable label', function (): void {
    expect(IssueStatus::Open->label())->toBe('Open')
        ->and(IssueStatus::InProgress->label())->toBe('In Progress')
        ->and(IssueStatus::Closed->label())->toBe('Closed');
});

it('exposes all backing values', function (): void {
    expect(IssueStatus::values())->toBe(['open', 'in_progress', 'closed']);
});
