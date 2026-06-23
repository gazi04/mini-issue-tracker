<?php

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->issue = Issue::factory()->for(Project::factory()->for($this->user, 'owner'))->create();
});

it('assigns a member to an issue', function (): void {
    $member = User::factory()->create();

    $this->actingAs($this->user)
        ->postJson(route('issues.members.store', $this->issue), ['user_id' => $member->id])
        ->assertOk()
        ->assertJsonCount(1, 'data');

    $this->assertDatabaseHas('issue_user', [
        'issue_id' => $this->issue->id,
        'user_id' => $member->id,
    ]);
});

it('removes a member from an issue', function (): void {
    $member = User::factory()->create();
    $this->issue->members()->attach($member);

    $this->actingAs($this->user)
        ->deleteJson(route('issues.members.destroy', [$this->issue, $member]))
        ->assertOk()
        ->assertJsonCount(0, 'data');

    $this->assertDatabaseMissing('issue_user', [
        'issue_id' => $this->issue->id,
        'user_id' => $member->id,
    ]);
});

it('rejects assigning a user that does not exist', function (): void {
    $this->actingAs($this->user)
        ->postJson(route('issues.members.store', $this->issue), ['user_id' => 99999])
        ->assertStatus(422)
        ->assertJsonValidationErrors('user_id');
});

it('assigns the same member only once', function (): void {
    $member = User::factory()->create();

    $this->actingAs($this->user)
        ->postJson(route('issues.members.store', $this->issue), ['user_id' => $member->id])
        ->assertOk();

    $this->actingAs($this->user)
        ->postJson(route('issues.members.store', $this->issue), ['user_id' => $member->id])
        ->assertOk()
        ->assertJsonCount(1, 'data');

    expect($this->issue->members()->count())->toBe(1);
});
