<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $issue->title }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('issues.edit', $issue) }}" class="text-sm text-indigo-600 hover:underline">Edit</a>
                <form method="POST" action="{{ route('issues.destroy', $issue) }}"
                    onsubmit="return confirm('Delete this issue?');">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm text-red-600 hover:underline">Delete</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12"
        data-issue-root
        data-issue-id="{{ $issue->id }}"
        data-tags-url="{{ route('issues.tags.store', $issue) }}"
        data-members-url="{{ route('issues.members.store', $issue) }}"
        data-comments-url="{{ route('issues.comments.index', $issue) }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="px-4 py-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            {{-- Issue meta --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-700">{{ $issue->description ?: 'No description.' }}</p>
                <dl class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Project</dt>
                        <dd><a href="{{ route('projects.show', $issue->project) }}" class="text-indigo-600 hover:underline">{{ $issue->project->name }}</a></dd>
                    </div>
                    <div><dt class="text-gray-500">Status</dt><dd>{{ $issue->status->label() }}</dd></div>
                    <div><dt class="text-gray-500">Priority</dt><dd>{{ $issue->priority->label() }}</dd></div>
                    <div><dt class="text-gray-500">Due date</dt><dd>{{ $issue->due_date?->format('M j, Y') ?? '—' }}</dd></div>
                </dl>
            </div>

            {{-- Tags --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-3">Tags</h3>
                <div id="issue-tags" class="flex flex-wrap gap-2 mb-3">
                    @foreach ($issue->tags as $tag)
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded text-white" style="background-color: {{ $tag->color ?? '#6b7280' }}">
                            {{ $tag->name }}
                            <button type="button" data-detach-tag="{{ $tag->id }}" class="font-bold hover:text-gray-200">&times;</button>
                        </span>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <select id="tag-select" class="border-gray-300 rounded-md shadow-sm text-sm">
                        @foreach ($allTags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="attach-tag-btn" class="px-3 py-1 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Attach</button>
                </div>
            </div>

            {{-- Members --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-3">Members</h3>
                <ul id="issue-members" class="space-y-2 mb-3">
                    @foreach ($issue->members as $member)
                        <li class="flex items-center justify-between text-sm">
                            <span>{{ $member->name }}</span>
                            <button type="button" data-detach-member="{{ $member->id }}" class="text-red-600 hover:underline">Remove</button>
                        </li>
                    @endforeach
                </ul>
                <div class="flex gap-2">
                    <select id="member-select" class="border-gray-300 rounded-md shadow-sm text-sm">
                        @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="attach-member-btn" class="px-3 py-1 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Assign</button>
                </div>
            </div>

            {{-- Comments --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-3">Comments</h3>

                <form id="comment-form" action="{{ route('issues.comments.store', $issue) }}" class="space-y-3 mb-6">
                    <div>
                        <x-input-label for="author_name" :value="__('Your name')" />
                        <x-text-input id="author_name" name="author_name" type="text" class="mt-1 block w-full" />
                        <p class="mt-1 text-sm text-red-600" data-error="author_name"></p>
                    </div>
                    <div>
                        <x-input-label for="body" :value="__('Comment')" />
                        <textarea id="body" name="body" rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        <p class="mt-1 text-sm text-red-600" data-error="body"></p>
                    </div>
                    <x-primary-button>{{ __('Add Comment') }}</x-primary-button>
                </form>

                <ul id="comment-list" class="space-y-3"></ul>
                <button type="button" id="load-more-comments" class="mt-4 text-sm text-indigo-600 hover:underline hidden">Load more</button>
            </div>
        </div>
    </div>
</x-app-layout>
