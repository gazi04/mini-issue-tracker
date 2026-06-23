<?php

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;

it('relates a project to its issues and owner', function (): void {
    $owner = User::factory()->create();
    $project = Project::factory()->for($owner, 'owner')->create();
    Issue::factory()->count(2)->for($project)->create();

    expect($project->issues)->toHaveCount(2)
        ->and($project->owner->is($owner))->toBeTrue();
});

it('relates an issue to its project, comments, tags and members', function (): void {
    $issue = Issue::factory()->for(Project::factory())->create();
    Comment::factory()->count(3)->for($issue)->create();
    $issue->tags()->attach(Tag::factory()->create());
    $issue->members()->attach(User::factory()->create());

    expect($issue->project)->toBeInstanceOf(Project::class)
        ->and($issue->comments)->toHaveCount(3)
        ->and($issue->tags)->toHaveCount(1)
        ->and($issue->members)->toHaveCount(1);
});

it('relates a tag to its issues', function (): void {
    $tag = Tag::factory()->create();
    Issue::factory()->for(Project::factory())->create()->tags()->attach($tag);

    expect($tag->issues)->toHaveCount(1);
});

it('relates a comment to its issue', function (): void {
    $comment = Comment::factory()->for(Issue::factory()->for(Project::factory()))->create();

    expect($comment->issue)->toBeInstanceOf(Issue::class);
});

it('casts issue status and priority to enums', function (): void {
    $issue = Issue::factory()->for(Project::factory())->create([
        'status' => IssueStatus::InProgress,
        'priority' => IssuePriority::High,
    ]);

    $fresh = $issue->fresh();

    expect($fresh->status)->toBeInstanceOf(IssueStatus::class)
        ->and($fresh->status)->toBe(IssueStatus::InProgress)
        ->and($fresh->priority)->toBeInstanceOf(IssuePriority::class)
        ->and($fresh->priority)->toBe(IssuePriority::High);
});
