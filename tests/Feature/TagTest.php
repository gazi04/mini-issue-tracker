<?php

use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->issue = Issue::factory()->for(Project::factory()->for($this->user, 'owner'))->create();
});

it('creates a unique tag via ajax', function (): void {
    $this->actingAs($this->user)
        ->postJson(route('tags.store'), ['name' => 'backend', 'color' => '#111111'])
        ->assertCreated()
        ->assertJsonPath('data.name', 'backend');

    $this->assertDatabaseHas('tags', ['name' => 'backend']);
});

it('rejects a duplicate tag name', function (): void {
    Tag::factory()->create(['name' => 'backend']);

    $this->actingAs($this->user)
        ->postJson(route('tags.store'), ['name' => 'backend'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('name');
});

it('attaches a tag to an issue', function (): void {
    $tag = Tag::factory()->create();

    $this->actingAs($this->user)
        ->postJson(route('issues.tags.store', $this->issue), ['tag_id' => $tag->id])
        ->assertOk()
        ->assertJsonCount(1, 'data');

    $this->assertDatabaseHas('issue_tag', [
        'issue_id' => $this->issue->id,
        'tag_id' => $tag->id,
    ]);
});

it('detaches a tag from an issue', function (): void {
    $tag = Tag::factory()->create();
    $this->issue->tags()->attach($tag);

    $this->actingAs($this->user)
        ->deleteJson(route('issues.tags.destroy', [$this->issue, $tag]))
        ->assertOk()
        ->assertJsonCount(0, 'data');

    $this->assertDatabaseMissing('issue_tag', [
        'issue_id' => $this->issue->id,
        'tag_id' => $tag->id,
    ]);
});

it('lists all tags', function (): void {
    Tag::factory()->create(['name' => 'frontend']);
    Tag::factory()->create(['name' => 'backend']);

    $this->actingAs($this->user)
        ->getJson(route('tags.index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['name' => 'frontend'])
        ->assertJsonFragment(['name' => 'backend']);
});

it('creates a tag without a color', function (): void {
    $this->actingAs($this->user)
        ->postJson(route('tags.store'), ['name' => 'no-color'])
        ->assertCreated();

    $this->assertDatabaseHas('tags', ['name' => 'no-color', 'color' => null]);
});

it('rejects attaching a tag that does not exist', function (): void {
    $this->actingAs($this->user)
        ->postJson(route('issues.tags.store', $this->issue), ['tag_id' => 99999])
        ->assertStatus(422)
        ->assertJsonValidationErrors('tag_id');
});

it('attaches the same tag only once', function (): void {
    $tag = Tag::factory()->create();

    $this->actingAs($this->user)
        ->postJson(route('issues.tags.store', $this->issue), ['tag_id' => $tag->id])
        ->assertOk();

    $this->actingAs($this->user)
        ->postJson(route('issues.tags.store', $this->issue), ['tag_id' => $tag->id])
        ->assertOk()
        ->assertJsonCount(1, 'data');

    expect($this->issue->tags()->count())->toBe(1);
});
