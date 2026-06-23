<?php

namespace App\Http\Controllers;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(Request $request): View
    {
        $issues = $this->filteredQuery($request)
            ->with(['project', 'tags'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('issues.index', [
            'issues' => $issues,
            'tags' => Tag::query()->orderBy('name')->get(),
            'statuses' => IssueStatus::cases(),
            'priorities' => IssuePriority::cases(),
            'filters' => $request->only(['status', 'priority', 'tag', 'q']),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $issues = $this->filteredQuery($request)
            ->with(['project', 'tags'])
            ->latest()
            ->limit(25)
            ->get()
            ->map(fn (Issue $issue): array => [
                'id' => $issue->id,
                'title' => $issue->title,
                'status' => $issue->status->value,
                'priority' => $issue->priority->value,
                'project' => $issue->project->name,
                'url' => route('issues.show', $issue),
                'tags' => $issue->tags->map(fn (Tag $tag): array => [
                    'name' => $tag->name,
                    'color' => $tag->color,
                ])->all(),
            ]);

        return response()->json(['data' => $issues]);
    }

    public function create(Request $request): View
    {
        return view('issues.create', [
            'projects' => Project::query()->orderBy('name')->get(),
            'statuses' => IssueStatus::cases(),
            'priorities' => IssuePriority::cases(),
            'selectedProjectId' => $request->integer('project_id') ?: null,
        ]);
    }

    public function store(StoreIssueRequest $request): RedirectResponse
    {
        $issue = Issue::query()->create($request->validated());

        return to_route('issues.show', $issue)
            ->with('status', 'Issue created.');
    }

    public function show(Issue $issue): View
    {
        $issue->load(['project', 'tags', 'members']);

        return view('issues.show', [
            'issue' => $issue,
            'allTags' => Tag::query()->orderBy('name')->get(),
            'allUsers' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function edit(Issue $issue): View
    {
        return view('issues.edit', [
            'issue' => $issue,
            'projects' => Project::query()->orderBy('name')->get(),
            'statuses' => IssueStatus::cases(),
            'priorities' => IssuePriority::cases(),
        ]);
    }

    public function update(UpdateIssueRequest $request, Issue $issue): RedirectResponse
    {
        $issue->update($request->validated());

        return to_route('issues.show', $issue)
            ->with('status', 'Issue updated.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $issue->delete();

        return to_route('issues.index')
            ->with('status', 'Issue deleted.');
    }

    private function filteredQuery(Request $request): Builder
    {
        return Issue::query()
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')))
            ->when($request->filled('priority'), fn (Builder $query) => $query->where('priority', $request->string('priority')))
            ->when($request->filled('tag'), fn (Builder $query) => $query->whereHas('tags', fn (Builder $tagQuery) => $tagQuery->where('tags.id', $request->integer('tag'))))
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = $request->string('q');
                $query->where(fn (Builder $inner) => $inner
                    ->where('title', 'like', sprintf('%%%s%%', $term))
                    ->orWhere('description', 'like', sprintf('%%%s%%', $term)));
            });
    }
}
