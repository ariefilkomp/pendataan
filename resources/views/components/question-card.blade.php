<div class="flex justify-between mt-2 p-2 sm:p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-200 dark:border-gray-700">
    <div>
        <p class="text-gray-700 text-base ">{{ $question?->question }}</p>
        <div class="flex gap-4">
            <p class="text-gray-500 text-sm ">{{ $question?->column_name }}</p>
            <p class="text-gray-500 text-sm ">{{ $question?->type }}</p>
            <p class="text-gray-500 text-sm ">{{ $question?->is_required ? 'required' : 'not required' }}</p>
        </div>
    </div>
    <x-secondary-button type="button" class="mt-4" x-on:click.prevent="$dispatch('open-modal', 'edit-question-modal') " onclick="setUpdateData({{ json_encode($question) }})">
        Edit
    </x-secondary-button>
</div>