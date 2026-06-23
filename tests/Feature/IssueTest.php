<?php

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->project = Project::factory()->for($this->user, 'owner')->create();
});

it('creates an issue', function (): void {
    $this->actingAs($this->user)
        ->post(route('issues.store'), [
            'project_id' => $this->project->id,
            'title' => 'Login is broken',
            'description' => 'Steps to reproduce',
            'status' => IssueStatus::Open->value,
            'priority' => IssuePriority::High->value,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('issues', [
        'title' => 'Login is broken',
        'status' => 'open',
        'priority' => 'high',
    ]);
});

it('rejects an invalid status', function (): void {
    $this->actingAs($this->user)
        ->post(route('issues.store'), [
            'project_id' => $this->project->id,
            'title' => 'Bad status',
            'status' => 'nonsense',
            'priority' => IssuePriority::Low->value,
        ])
        ->assertSessionHasErrors('status');
});

it('filters issues by status', function (): void {
    Issue::factory()->for($this->project)->create(['status' => IssueStatus::Open, 'title' => 'Open one']);
    Issue::factory()->for($this->project)->create(['status' => IssueStatus::Closed, 'title' => 'Closed one']);

    $this->actingAs($this->user)
        ->get(route('issues.index', ['status' => 'open']))
        ->assertOk()
        ->assertSee('Open one')
        ->assertDontSee('Closed one');
});

it('filters issues by priority', function (): void {
    Issue::factory()->for($this->project)->create(['priority' => IssuePriority::High, 'title' => 'High one']);
    Issue::factory()->for($this->project)->create(['priority' => IssuePriority::Low, 'title' => 'Low one']);

    $this->actingAs($this->user)
        ->get(route('issues.index', ['priority' => 'high']))
        ->assertOk()
        ->assertSee('High one')
        ->assertDontSee('Low one');
});

it('filters issues by tag', function (): void {
    $tag = Tag::factory()->create();
    $tagged = Issue::factory()->for($this->project)->create(['title' => 'Tagged one']);
    $tagged->tags()->attach($tag);
    Issue::factory()->for($this->project)->create(['title' => 'Untagged one']);

    $this->actingAs($this->user)
        ->get(route('issues.index', ['tag' => $tag->id]))
        ->assertOk()
        ->assertSee('Tagged one')
        ->assertDontSee('Untagged one');
});

it('searches issues by title via ajax and returns only the results fragment', function (): void {
    Issue::factory()->for($this->project)->create(['title' => 'Payment gateway down']);
    Issue::factory()->for($this->project)->create(['title' => 'Unrelated thing']);

    $this->actingAs($this->user)
        ->get(route('issues.index', ['q' => 'gateway']), ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()
        ->assertSee('Payment gateway down')
        ->assertDontSee('Unrelated thing')
        ->assertDontSee('New Issue'); // fragment only, not the full layout
});

it('composes search with a status filter via ajax', function (): void {
    Issue::factory()->for($this->project)->create(['title' => 'Gateway open', 'status' => IssueStatus::Open]);
    Issue::factory()->for($this->project)->create(['title' => 'Gateway closed', 'status' => IssueStatus::Closed]);

    $this->actingAs($this->user)
        ->get(route('issues.index', ['q' => 'Gateway', 'status' => 'open']), ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()
        ->assertSee('Gateway open')
        ->assertDontSee('Gateway closed');
});

it('returns a paginated results fragment via ajax', function (): void {
    Issue::factory()->count(15)->for($this->project)->create();

    $this->actingAs($this->user)
        ->get(route('issues.index', ['page' => 2]), ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()
        ->assertDontSee('New Issue')
        ->assertSee('page=1'); // paginator "previous" link present on page 2
});

it('updates an issue', function (): void {
    $issue = Issue::factory()->for($this->project)->create();

    $this->actingAs($this->user)
        ->put(route('issues.update', $issue), [
            'project_id' => $this->project->id,
            'title' => 'Updated title',
            'status' => IssueStatus::InProgress->value,
            'priority' => IssuePriority::Medium->value,
        ])
        ->assertRedirect();

    expect($issue->fresh()->title)->toBe('Updated title')
        ->and($issue->fresh()->status)->toBe(IssueStatus::InProgress);
});

it('requires a title and project when creating an issue', function (): void {
    $this->actingAs($this->user)
        ->post(route('issues.store'), [
            'status' => IssueStatus::Open->value,
            'priority' => IssuePriority::Low->value,
        ])
        ->assertSessionHasErrors(['title', 'project_id']);
});

it('rejects a project that does not exist', function (): void {
    $this->actingAs($this->user)
        ->post(route('issues.store'), [
            'project_id' => 99999,
            'title' => 'Orphan issue',
            'status' => IssueStatus::Open->value,
            'priority' => IssuePriority::Low->value,
        ])
        ->assertSessionHasErrors('project_id');
});

it('rejects an invalid priority', function (): void {
    $this->actingAs($this->user)
        ->post(route('issues.store'), [
            'project_id' => $this->project->id,
            'title' => 'Bad priority',
            'status' => IssueStatus::Open->value,
            'priority' => 'urgent',
        ])
        ->assertSessionHasErrors('priority');
});

it('accepts and persists a due date', function (): void {
    $this->actingAs($this->user)
        ->post(route('issues.store'), [
            'project_id' => $this->project->id,
            'title' => 'Dated issue',
            'status' => IssueStatus::Open->value,
            'priority' => IssuePriority::Low->value,
            'due_date' => '2026-12-31',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $issue = Issue::query()->where('title', 'Dated issue')->first();

    expect($issue)->not->toBeNull()
        ->and($issue->due_date->toDateString())->toBe('2026-12-31');
});

it('shows an issue detail page', function (): void {
    $issue = Issue::factory()->for($this->project)->create(['title' => 'Detail view']);

    $this->actingAs($this->user)
        ->get(route('issues.show', $issue))
        ->assertOk()
        ->assertSee('Detail view');
});

it('deletes an issue', function (): void {
    $issue = Issue::factory()->for($this->project)->create();

    $this->actingAs($this->user)
        ->delete(route('issues.destroy', $issue))
        ->assertRedirect(route('issues.index'));

    $this->assertDatabaseMissing('issues', ['id' => $issue->id]);
});
