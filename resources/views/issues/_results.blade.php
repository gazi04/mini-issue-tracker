<div id="issue-list" class="divide-y">
    @forelse ($issues as $issue)
        <div class="flex items-center justify-between py-3">
            <div>
                <a href="{{ route('issues.show', $issue) }}" class="font-medium text-indigo-600 hover:underline">{{ $issue->title }}</a>
                <p class="text-xs text-gray-500">{{ $issue->project->name }}</p>
                <div class="mt-1 flex flex-wrap gap-1">
                    @foreach ($issue->tags as $tag)
                        <span class="text-xs px-2 py-0.5 rounded text-white" style="background-color: {{ $tag->color ?? '#6b7280' }}">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
            <div class="text-right space-y-1">
                <span class="block text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">{{ $issue->status->label() }}</span>
                <span class="block text-xs text-gray-500">{{ $issue->priority->label() }}</span>
            </div>
        </div>
    @empty
        <p class="py-3 text-gray-500">No issues match.</p>
    @endforelse
</div>

<div class="mt-4">
    {{ $issues->links() }}
</div>
