<?php

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->issue = Issue::factory()->for(Project::factory()->for($this->user, 'owner'))->create();
});

it('lists comments paginated via ajax', function (): void {
    Comment::factory()->count(7)->for($this->issue)->create();

    $this->actingAs($this->user)
        ->getJson(route('issues.comments.index', $this->issue))
        ->assertOk()
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('total', 7);
});

it('stores a comment via ajax', function (): void {
    $this->actingAs($this->user)
        ->postJson(route('issues.comments.store', $this->issue), [
            'author_name' => 'Jane',
            'body' => 'Looks good to me.',
        ])
        ->assertCreated()
        ->assertJsonPath('data.author_name', 'Jane');

    $this->assertDatabaseHas('comments', [
        'issue_id' => $this->issue->id,
        'author_name' => 'Jane',
    ]);
});

it('validates author name and body when storing a comment', function (): void {
    $this->actingAs($this->user)
        ->postJson(route('issues.comments.store', $this->issue), [
            'author_name' => '',
            'body' => '',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['author_name', 'body']);
});

it('returns comments newest first', function (): void {
    $older = Comment::factory()->for($this->issue)->create([
        'author_name' => 'Older',
        'created_at' => now()->subMinute(),
    ]);
    $newer = Comment::factory()->for($this->issue)->create([
        'author_name' => 'Newer',
        'created_at' => now(),
    ]);

    $this->actingAs($this->user)
        ->getJson(route('issues.comments.index', $this->issue))
        ->assertOk()
        ->assertJsonPath('data.0.id', $newer->id)
        ->assertJsonPath('data.1.id', $older->id);
});

it('exposes a next page url when more comments remain', function (): void {
    Comment::factory()->count(7)->for($this->issue)->create();

    $response = $this->actingAs($this->user)
        ->getJson(route('issues.comments.index', $this->issue))
        ->assertOk()
        ->assertJsonPath('current_page', 1);

    expect($response->json('next_page_url'))->not->toBeNull();
});
