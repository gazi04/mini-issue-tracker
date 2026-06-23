@csrf
<div class="space-y-4">
    <div>
        <x-input-label for="project_id" :value="__('Project')" />
        <select id="project_id" name="project_id"
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach ($projects as $project)
                <option value="{{ $project->id }}"
                    @selected((int) old('project_id', $issue->project_id ?? ($selectedProjectId ?? null)) === $project->id)>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
            :value="old('title', $issue->title ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="4"
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $issue->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}"
                        @selected(old('status', $issue->status->value ?? 'open') === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="priority" :value="__('Priority')" />
            <select id="priority" name="priority"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                @foreach ($priorities as $priority)
                    <option value="{{ $priority->value }}"
                        @selected(old('priority', $issue->priority->value ?? 'medium') === $priority->value)>
                        {{ $priority->label() }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="due_date" :value="__('Due date')" />
            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full"
                :value="old('due_date', isset($issue->due_date) ? $issue->due_date->format('Y-m-d') : '')" />
            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('issues.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
    </div>
</div>
