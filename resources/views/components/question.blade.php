<div class="mt-4 max-w-xl m-auto bg-slate-100 p-4 rounded">
    <p class="text-gray-700 text-base ">{{ $question?->question }} {!! $question?->is_required ? '<span class="text-red-500">*</span>' : '' !!}</p>

    <div class="flex gap-4">
        @if($question?->type == 'short_answer')
            <x-text-input name="{{ $question?->column_name }}" type="text" class="mt-1 block w-full" autofocus autocomplete="{{ $question?->column_name }}" value="{{ old($question?->column_name, $answers?->{$question?->column_name}) }}" />
            <x-input-error class="mt-2" :messages="$errors->get( $question?->column_name )" />
        @endif
        
        @if($question?->type == 'paragraph')
            <x-textarea-input name="{{ $question?->column_name }}" class="mt-1 block w-full"  >{{old($question?->column_name, $answers?->{$question?->column_name})}}</x-textarea-input>
            <x-input-error class="mt-2" :messages="$errors->get( $question?->column_name )" />
        @endif

        @if($question?->type == 'multiple_choice')
            @php
                $options = json_decode($question->options);
            @endphp
            <div class="flex flex-col">
                @foreach($options as $index => $option)
                    <div class="flex items-center mt-4">
                        <input id="default-radio-{{ $index }}" type="radio" value="{{ $option }}" name="{{ $question?->column_name }}" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" @if($answers?->{$question?->column_name} == $option) checked @endif)>
                        <label for="default-radio-{{ $index }}" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $option }}</label>
                    </div>
                @endforeach
                <x-input-error class="mt-2" :messages="$errors->get($question?->column_name)" />
            </div>
        @endif

        @if($question?->type == 'checkboxes')
            @php
                $options = json_decode($question->options);
            @endphp
            <div class="flex flex-col">
                @foreach($options as $index => $option)
                    <div class="flex items-center mt-4">
                        <input id="default-checkbox-{{ $index }}" type="checkbox" value="{{ $option }}" name="{{ $question?->column_name }}[]" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" @if($answers?->{$question?->column_name} == $option) checked @endif)>
                        <label for="default-checkbox-{{ $index }}" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $option }}</label>
                    </div>
                @endforeach
            </div>
            <x-input-error class="mt-2" :messages="$errors->get($question?->column_name)" />
        @endif

        @if($question?->type == 'dropdown')
            @php
                $options = json_decode($question?->options);
            @endphp
            <select name="{{ $question?->column_name }}" id="{{ $question?->column_name }}" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                @foreach($options as $index => $option)
                    <option value="{{ $option }}" @if(old($question?->column_name, $answers?->{$question?->column_name}) == $option ) selected @endif>{{ $option }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get($question?->column_name)" />
        @endif

        @if($question?->type == 'file')
            <input name="{{ $question?->column_name }}" type="file" accept="image/*, .pdf, .doc, .docx" class="mt-1 block w-full bg-white" autofocus autocomplete="{{ $question?->column_name }}" value="{{ old($question?->column_name, $answers?->{$question?->column_name}) }}" />
            <x-input-error class="mt-2" :messages="$errors->get( $question?->column_name )" />
        @endif

        @if($question?->type == 'date')
            <input name="{{ $question?->column_name }}" type="date" value="{{ old($question?->column_name, $answers?->{$question?->column_name}) }}" class="mt-1 block w-full bg-white border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" autofocus autocomplete="{{ $question?->column_name }}" value="{{ old($question?->column_name, $answers?->{$question?->column_name}) }}" />
            <x-input-error class="mt-2" :messages="$errors->get( $question?->column_name )" />
        @endif

        @if($question?->type == 'time')
            <input name="{{ $question?->column_name }}" type="time" value="{{ old($question?->column_name, $answers?->{$question?->column_name}) }}" class="mt-1 block w-full bg-white border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" autofocus autocomplete="{{ $question?->column_name }}" value="{{ old($question?->column_name, $answers?->{$question?->column_name}) }}" />
            <x-input-error class="mt-2" :messages="$errors->get( $question?->column_name )" />
        @endif



    </div>
</div>