<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Issues') }}</h2>
            <a href="{{ route('issues.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('New Issue') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="px-4 py-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <input type="text" id="issue-search" placeholder="Search issues by title or description…"
                    data-search-url="{{ route('issues.search') }}"
                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
            </div>

            <form method="GET" action="{{ route('issues.index') }}" class="bg-white shadow-sm sm:rounded-lg p-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
                <select name="status" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <select name="priority" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">All priorities</option>
                    @foreach ($priorities as $priority)
                        <option value="{{ $priority->value }}" @selected(($filters['priority'] ?? '') === $priority->value)>{{ $priority->label() }}</option>
                    @endforeach
                </select>
                <select name="tag" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">All tags</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" @selected((string) ($filters['tag'] ?? '') === (string) $tag->id)>{{ $tag->name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <x-primary-button>{{ __('Filter') }}</x-primary-button>
                    <a href="{{ route('issues.index') }}" class="inline-flex items-center px-3 text-sm text-gray-600 hover:text-gray-900">Reset</a>
                </div>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
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

                <div id="issue-pagination" class="mt-4">
                    {{ $issues->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
