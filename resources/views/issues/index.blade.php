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

            <form id="issue-filters" method="GET" action="{{ route('issues.index') }}" class="bg-white shadow-sm sm:rounded-lg p-4 space-y-3">
                <input type="text" id="issue-search" name="q" value="{{ $filters['q'] ?? '' }}"
                    placeholder="Search issues by title or description…"
                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
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
                </div>
            </form>

            <div id="issue-results" class="bg-white shadow-sm sm:rounded-lg p-6">
                @include('issues._results')
            </div>
        </div>
    </div>
</x-app-layout>
