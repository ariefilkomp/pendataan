<div class="mt-4 max-w-xl m-auto bg-slate-100 p-4 rounded">
    <p class="text-gray-700 text-base ">{{ $question?->question }} {!! $question?->is_required ? '<span class="text-red-500">*</span>' : '' !!}</p>

    <div class="flex gap-4">
        @if($question?->type == 'short_answer')
            <x-text-input name="{{ $question?->column_name }}" type="text" class="mt-1 block w-full" autofocus autocomplete="column_name" value="{{ old($question?->column_name, $answers->{$question?->column_name}) }}" />
            <x-input-error class="mt-2" :messages="$errors->get('column_name')" />
        @endif

        @if($question?->type == 'paragraph')
            <x-textarea-input name="{{ $question?->column_name }}" class="mt-1 block w-full"  >{{old($question?->column_name, $answers->{$question?->column_name})}}</x-textarea-input>
        @endif

        @if($question?->type == 'multiple_choice')
            @php
                $options = json_decode($question->options);
            @endphp
            <div class="flex flex-col">
            @foreach($options as $index => $option)
                <div class="flex items-center mt-4">
                    <input id="default-radio-{{ $index }}" type="radio" value="{{ $option }}" name="{{ $question?->column_name }}" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" @if($answers->{$question?->column_name} == $option) checked @endif)>
                    <label for="default-radio-{{ $index }}" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $option }}</label>
                </div>
            @endforeach
            </div>
        @endif

        @if($question?->type == 'checkboxes')
            <x-textarea-input name="{{ $question?->column_name }}" class="mt-1 block w-full"  ></x-textarea-input>
        @endif

    </div>
</div>