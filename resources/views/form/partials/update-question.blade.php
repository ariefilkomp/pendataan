<section>
    <div class="mt-6 px-2">
        <p class="text-gray-700 text-base">{{ $form->sections->where('id', $section_id)->first()?->description }}</p>
        @if($form->sections->count() > 0)
        <form method="post" action="{{ route('add-question') }}" class="p-6" x-data="dynamicForm()">
            @if (session('status') === 'question-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Question Updated.') }}</p>
            @endif

            @if (session('status') === 'question-created')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Question Created.') }}</p>
            @endif

            @foreach ($form->questions->sortBy('created_at') as $question)
                @if ($question->section_id == $section_id)
                    <x-question-card :question="$question" />
                @endif
            @endforeach
            @if ($errors->addQuestion->isNotEmpty())
                @foreach ($errors->addQuestion->all() as $message)
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @endforeach
            @endif


            @csrf
            <input type="hidden" name="form_id" value="{{ $form->id }}">
            <input type="hidden" name="section_id" value="{{ $section_id }}">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-4">
                {{ __('Add Question') }}
            </h2>

            <div class="mt-4">
                <x-input-label for="question" :value="__('Question *')" />
                <x-textarea-input id="question" name="question" type="text" class="mt-1 block w-full" required
                    autofocus autocomplete="question">{{ old('question') }}</x-textarea-input>
                <x-input-error class="mt-2" :messages="$errors->get('question')" />
            </div>

            <div class="flex gap-8">
                <div class="mt-4">
                    <x-input-label for="column_name" :value="__('Column Name')" />
                    <x-text-input id="column_name" name="column_name" type="text" class="mt-1 block w-full"
                        :value="old('column_name')" autofocus autocomplete="column_name" />
                    <x-input-error class="mt-2" :messages="$errors->get('column_name')" />
                </div>

                <div class="mt-4">
                    <x-input-label for="type" :value="__('Type')" />
                    <x-select-input name="type" class="mt-1 block w-full"
                        x-on:change="typeChange($event.target.value)">
                        @foreach ($optType as $key => $value)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                {{ $value }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                </div>
                <div class="mt-4">
                    <label class="cursor-pointer">
                        <span class="block font-medium text-sm text-gray-700 dark:text-gray-300">Required</span>
                    <input type="checkbox" value="1" name="is_required"
                            class="sr-only peer">
                        <div
                            class="mt-2 relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                        </div>
                    </label>
                </div>
            </div>

            <div class="mt-4" id="options" x-show="addInputShow">
                <div class="flex flex-col text-center items-center">

                    <div id="inputContainer" class="w-full">
                        <div class="flex gap-4 w-full input-group">
                            <x-text-input name="options[]" placeholder="Isi Opsi"
                                type="text" class="mt-1 block w-full" autocomplete="option[]" />
                            <p class="pt-2">
                                <x-danger-button type="button"
                                    class="text-xs rounded m-0 remove-btn">&times;</x-danger-button>
                            </p>
                        </div>
                    </div>
                    <button type="button" id="addInput"
                        class="text-xs rounded mt-2 p-2 border border-collapse w-24 ">Add Input</button>
                    <x-input-error class="mt-2" :messages="$errors->get('options')" />
                </div>
            </div>


            <div class="mt-6 flex justify-end">
                <x-primary-button class="ms-3">
                    {{ __('Add Question') }}
                </x-primary-button>
            </div>

            <script>
                $(document).ready(function() {
                    // Handle add input button
                    $('#addInput').on('click', function() {
                        const newInput = `
                            <div class="flex gap-4 w-full input-group">
                                <x-text-input name="options[]"
                                    type="text" class="mt-1 block w-full" placeholder="Isi Opsi" />
                                <p class="pt-2">
                                    <x-danger-button type="button"
                                        class="text-xs rounded m-0 remove-btn">&times;</x-danger-button>
                                </p>
                            </div>
                        `;
                        $('#inputContainer').append(newInput);
                    });

                    $('#addInput_update').on('click', function() {
                        const newInput = `
                            <div class="flex gap-4 w-full input-group">
                                <x-text-input name="options[]"
                                    type="text" class="mt-1 block w-full" placeholder="Isi Opsi" />
                                <p class="pt-2">
                                    <x-danger-button type="button"
                                        class="text-xs rounded m-0 remove-btn">&times;</x-danger-button>
                                </p>
                            </div>
                        `;
                        $('#inputContainer_update').append(newInput);
                    });

                    // Handle remove input button
                    $(document).on('click', '.remove-btn', function() {
                        $(this).closest('.input-group').remove();
                    });

                    // Handle form submission
                    $('#dynamicForm').on('submit', function(e) {
                        e.preventDefault(); // Prevent default form submission

                        const formData = $(this).serializeArray();
                        console.log('Form Data:', formData);

                        alert('Form submitted! Check console for data.');
                    });
                });

                function dynamicForm() {
                    return {
                        inputs: [
                            @if (old('options'))
                                @foreach (old('options') as $option)
                                    {
                                        id: {{ $loop->iteration }},
                                        value: '{{ $option }}',
                                        placeholder: 'Option {{ $loop->iteration }}'
                                    },
                                @endforeach
                            @else
                                inputOpt
                            @endif
                        ],
                        addInputShow: false,
                        addInput() {
                            this.inputs.push({
                                id: this.inputs.length + 1,
                                value: '',
                                placeholder: `Input ${this.inputs.length + 1}`
                            })
                        },
                        removeField(index) {
                            this.inputs.splice(index, 1);
                        },
                        typeChange(type) {
                            if (type == 'multiple_choice' || type == 'checkboxes' || type == 'dropdown') {
                                this.addInputShow = true;
                            } else {
                                this.addInputShow = false;
                            }
                        }
                    }
                }

                function setUpdateData(data) {

                    $('#question_id_update').val(data.id);
                    $('#question_id_delete').val(data.id);
                    $('#question_update').val(data.question);
                    $('#column_name_update').val(data.column_name);
                    $('#type_update').val(data.type);
                    $('#is_required_update').prop('checked', data.is_required);

                    if (data.type == 'multiple_choice' || data.type == 'checkboxes' || data.type == 'dropdown') {
                        $('#options_update').show();
                    }

                    if (data.options) {
                        $('#inputContainer_update').empty();
                        console.log('data.options', data.options);
                        let opt = JSON.parse(data.options);
                        console.log('opt', opt);
                        opt.forEach((option, index) => {
                            const newInput = `
                                <div class="flex gap-4 w-full input-group">
                                    <x-text-input name="options[]"
                                        type="text" class="mt-1 block w-full" placeholder="Isi Opsi" value="${option}" />
                                    <p class="pt-2">
                                        <x-danger-button type="button"
                                            class="text-xs rounded m-0 remove-btn">&times;</x-danger-button>
                                    </p>
                                </div>
                            `;
                            $('#inputContainer_update').append(newInput);
                        });
                    }

                }
            </script>

        </form>
        @endif
    </div>
</section>

<x-modal name="add-section-modal" :show="$errors->addSection->isNotEmpty()" focusable>
    <form method="post" action="{{ route('add-section') }}" class="p-6">
        @csrf
        <input type="hidden" name="form_id" value="{{ $form->id }}">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Add Section') }}
        </h2>

        <div>
            <x-input-label for="name" :value="__('Section Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Description')" />
            <x-textarea-input id="description" name="description" type="text" class="mt-1 block w-full" required
                autofocus autocomplete="description">{{ old('description') }}</x-textarea-input>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ms-3">
                {{ __('Add Section') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>

<x-modal name="delete-section-modal" :show="$errors->sectionDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('delete-section') }}" class="p-6">
        @csrf
        @method('delete')

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Anda yakin untuk menghapus section : ' . $form->sections->where('id', $section_id)->first()?->name . '?') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Once your section is deleted, all of its resources and data will be permanently deleted.') }}
        </p>

        <input type="hidden" name="section_id" value="{{ $section_id }}">
        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3">
                {{ __('Delete Section') }}
            </x-danger-button>
        </div>
    </form>
</x-modal>

<x-modal name="edit-question-modal" :show="$errors->updateQuestion->isNotEmpty()" focusable>
    <div class="flex justify-between px-6 pt-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Question') }}
        </h2>
        <form method="post" action="{{ route('delete-question') }}" onsubmit="return confirm('Are you sure you want to delete this question?');">
            @csrf
            @method('delete')
            <input type="hidden" name="question_id" value="" id="question_id_delete">
            <x-danger-button class="ms-3">
                {{ __('Delete Question') }}
            </x-danger-button>
        </form>
    </div>
    <form method="post" action="{{ route('update-question') }}" class="px-6 pb-6" x-data="dynamicFormUpdate()">
        @if ($errors->addQuestion->isNotEmpty())
            @foreach ($errors->addQuestion->all() as $message)
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @endforeach
        @endif


        @csrf
        <input type="hidden" name="form_id" value="{{ $form->id }}">
        <input type="hidden" name="section_id" value="{{ $section_id }}">
        <input type="hidden" name="question_id" value="" id="question_id_update"> 

        <div class="mt-4">
            <x-input-label for="question_update" :value="__('Question *')" />
            <x-textarea-input id="question_update" name="question" type="text" class="mt-1 block w-full" required
                autofocus autocomplete="question">{{ old('question') }}</x-textarea-input>
            <x-input-error class="mt-2" :messages="$errors->get('question')" />
        </div>

        <div class="flex gap-8">
            <div class="mt-4">
                <x-input-label for="column_name_update" :value="__('Column Name')" />
                <x-text-input id="column_name_update" name="column_name" type="text" class="mt-1 block w-full"
                    :value="old('column_name')" autofocus autocomplete="column_name" />
                <x-input-error class="mt-2" :messages="$errors->get('column_name')" />
            </div>

            <div class="mt-4">
                <x-input-label for="type" :value="__('Type')" />
                <x-select-input name="type" id="type_update" class="mt-1 block w-full"
                    x-on:change="typeChange($event.target.value)">
                    @foreach ($optType as $key => $value)
                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                            {{ $value }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error class="mt-2" :messages="$errors->get('type')" />
            </div>
            <div class="mt-4">
                <label class="cursor-pointer">
                    <span class="block font-medium text-sm text-gray-700 dark:text-gray-300">Required</span>
                    <input type="checkbox" id="is_required_update" value="1"
                        name="is_required" class="sr-only peer">
                    <div
                        class="mt-2 relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                    </div>
                </label>
            </div>
        </div>

        <div class="mt-4" id="options_update" x-show="addInputShow">
            <div class="flex flex-col text-center items-center">
                <div id="inputContainer_update" class="w-full">
                    
                </div>
                <button type="button" id="addInput_update"
                    class="text-xs rounded mt-2 p-2 border border-collapse w-24 ">Add Input</button>
                <x-input-error class="mt-2" :messages="$errors->get('options')" />
            </div>
        </div>


        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ms-3">
                {{ __('Update Question') }}
            </x-primary-button>
        </div>

        <script>
            function dynamicFormUpdate() {
                return {
                    inputs: [
                        @if (old('options'))
                            @foreach (old('options') as $option)
                                {
                                    id: {{ $loop->iteration }},
                                    value: '{{ $option }}',
                                    placeholder: 'Option {{ $loop->iteration }}'
                                },
                            @endforeach
                        @else
                            inputOpt
                        @endif
                    ],
                    addInputShow: false,
                    addInput() {
                        this.inputs.push({
                            id: this.inputs.length + 1,
                            value: '',
                            placeholder: `Input ${this.inputs.length + 1}`
                        })
                    },
                    removeField(index) {
                        this.inputs.splice(index, 1);
                    },
                    typeChange(type) {
                        if (type == 'multiple_choice' || type == 'checkboxes' || type == 'dropdown') {
                            this.addInputShow = true;
                        } else {
                            this.addInputShow = false;
                        }
                    },
                    setUpdateData(id) {
                        console.log('id', id);
                    }
                }
            }
        </script>

    </form>
</x-modal>

<x-modal name="edit-section-modal" :show="$errors->updateSection->isNotEmpty()" focusable>
    <form method="post" action="{{ route('edit-section') }}" class="p-6">
        @method('patch')
        @csrf
        <input type="hidden" name="form_id" value="{{ $form->id }}">
        <input type="hidden" name="section_id" value="{{ $section_id }}">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Edit Section') }}
        </h2>

        <div>
            <x-input-label for="name" :value="__('Section Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name',$form->sections->where('id', $section_id)->first()?->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Description')" />
            <x-textarea-input id="description" name="description" type="text" class="mt-1 block w-full" required
                autofocus autocomplete="description">{{ old('description',$form->sections->where('id', $section_id)->first()?->description) }}</x-textarea-input>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ms-3">
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>

<script>
    var inputOpt = {
        id: 1,
        value: 'Option 1',
        placeholder: 'Input 1'
    }

    var addInputShowUpdate = false;
</script>
