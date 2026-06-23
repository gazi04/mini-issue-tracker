<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $project->name }}</h2>
            <div class="flex items-center gap-2">
                @can('update', $project)
                    <a href="{{ route('projects.edit', $project) }}" class="text-sm text-indigo-600 hover:underline">Edit</a>
                @endcan
                @can('delete', $project)
                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                        onsubmit="return confirm('Delete this project and all its issues?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm text-red-600 hover:underline">Delete</button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="px-4 py-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-700">{{ $project->description ?: 'No description.' }}</p>
                <dl class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Owner</dt>
                        <dd class="text-gray-900">{{ $project->owner->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Start date</dt>
                        <dd class="text-gray-900">{{ $project->start_date?->format('M j, Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Deadline</dt>
                        <dd class="text-gray-900">{{ $project->deadline?->format('M j, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-lg text-gray-800">Issues</h3>
                    <a href="{{ route('issues.create', ['project_id' => $project->id]) }}" class="text-sm text-indigo-600 hover:underline">Add issue</a>
                </div>

                @forelse ($project->issues as $issue)
                    <div class="flex items-center justify-between py-2 border-b last:border-b-0">
                        <a href="{{ route('issues.show', $issue) }}" class="text-indigo-600 hover:underline">{{ $issue->title }}</a>
                        <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">{{ $issue->status->label() }}</span>
                    </div>
                @empty
                    <p class="text-gray-500">No issues in this project yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
