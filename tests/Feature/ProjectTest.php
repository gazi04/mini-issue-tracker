<?php

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('lists projects', function (): void {
    Project::factory()->for($this->user, 'owner')->create(['name' => 'Visible Project']);

    $this->actingAs($this->user)
        ->get(route('projects.index'))
        ->assertOk()
        ->assertSee('Visible Project');
});

it('creates a project owned by the authenticated user', function (): void {
    $this->actingAs($this->user)
        ->post(route('projects.store'), [
            'name' => 'New Project',
            'description' => 'Some description',
            'start_date' => '2026-01-01',
            'deadline' => '2026-02-01',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'name' => 'New Project',
        'user_id' => $this->user->id,
    ]);
});

it('validates the deadline is after the start date', function (): void {
    $this->actingAs($this->user)
        ->post(route('projects.store'), [
            'name' => 'Bad Dates',
            'start_date' => '2026-02-01',
            'deadline' => '2026-01-01',
        ])
        ->assertSessionHasErrors('deadline');
});

it('lets the owner update the project', function (): void {
    $project = Project::factory()->for($this->user, 'owner')->create();

    $this->actingAs($this->user)
        ->put(route('projects.update', $project), [
            'name' => 'Updated Name',
        ])
        ->assertRedirect();

    expect($project->fresh()->name)->toBe('Updated Name');
});

it('forbids a non-owner from updating the project', function (): void {
    $project = Project::factory()->for(User::factory(), 'owner')->create();

    $this->actingAs($this->user)
        ->put(route('projects.update', $project), ['name' => 'Hijacked'])
        ->assertForbidden();
});

it('forbids a non-owner from deleting the project', function (): void {
    $project = Project::factory()->for(User::factory(), 'owner')->create();

    $this->actingAs($this->user)
        ->delete(route('projects.destroy', $project))
        ->assertForbidden();

    $this->assertDatabaseHas('projects', ['id' => $project->id]);
});

it('lets the owner delete the project', function (): void {
    $project = Project::factory()->for($this->user, 'owner')->create();

    $this->actingAs($this->user)
        ->delete(route('projects.destroy', $project))
        ->assertRedirect(route('projects.index'));

    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

it('requires authentication', function (): void {
    $this->get(route('projects.index'))->assertRedirect(route('login'));
});

it('requires a name when creating a project', function (): void {
    $this->actingAs($this->user)
        ->post(route('projects.store'), ['description' => 'No name'])
        ->assertSessionHasErrors('name');
});

it('rejects an empty name on update', function (): void {
    $project = Project::factory()->for($this->user, 'owner')->create();

    $this->actingAs($this->user)
        ->put(route('projects.update', $project), ['name' => ''])
        ->assertSessionHasErrors('name');
});

it('shows a project with its issues', function (): void {
    $project = Project::factory()->for($this->user, 'owner')->create();
    Issue::factory()->for($project)->create(['title' => 'Listed issue']);

    $this->actingAs($this->user)
        ->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee('Listed issue');
});
