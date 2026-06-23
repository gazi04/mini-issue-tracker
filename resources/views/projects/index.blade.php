<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Projects') }}</h2>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('New Project') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @forelse ($projects as $project)
                        <div class="flex items-center justify-between py-3 border-b last:border-b-0">
                            <div>
                                <a href="{{ route('projects.show', $project) }}" class="font-semibold text-lg text-indigo-600 hover:underline">
                                    {{ $project->name }}
                                </a>
                                <p class="text-sm text-gray-500">
                                    {{ $project->issues_count }} {{ Str::plural('issue', $project->issues_count) }}
                                    &middot; owner: {{ $project->owner->name }}
                                </p>
                            </div>
                            <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-500 hover:text-gray-700">View</a>
                        </div>
                    @empty
                        <p class="text-gray-500">No projects yet. Create your first one.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
