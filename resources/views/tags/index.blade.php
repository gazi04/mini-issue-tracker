<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tags') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-3">Create tag</h3>
                <form id="tag-create-form" action="{{ route('tags.store') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="tag_name" :value="__('Name')" />
                        <x-text-input id="tag_name" name="name" type="text" class="mt-1 block" />
                        <p class="mt-1 text-sm text-red-600" data-error="name"></p>
                    </div>
                    <div>
                        <x-input-label for="tag_color" :value="__('Color')" />
                        <input id="tag_color" name="color" type="color" value="#3b82f6" class="mt-1 block h-10 w-16 border-gray-300 rounded-md" />
                    </div>
                    <x-primary-button>{{ __('Add Tag') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-3">All tags</h3>
                <div id="tag-list" class="flex flex-wrap gap-2">
                    @forelse ($tags as $tag)
                        <span class="text-xs px-2 py-1 rounded text-white" style="background-color: {{ $tag->color ?? '#6b7280' }}">{{ $tag->name }}</span>
                    @empty
                        <p class="text-gray-500">No tags yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
