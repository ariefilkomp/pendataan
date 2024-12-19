<section>
    <header class="flex gap-4">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Section '.$form->sections[0]->order. ' / '. $form->sections->count()) }}
        </h2>
        @if($form->sections[0]->order < $form->sections->count())
            <x-secondary-button>{{ __('Next') }}</x-secondary-button>
        @endif
        <x-secondary-button 
            x-data="" 
            x-on:click.prevent="$dispatch('open-modal', 'add-section-modal')">
            <svg class="w-2 h-2" fill="#000000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 45.402 45.402" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path d="M41.267,18.557H26.832V4.134C26.832,1.851,24.99,0,22.707,0c-2.283,0-4.124,1.851-4.124,4.135v14.432H4.141 c-2.283,0-4.139,1.851-4.138,4.135c-0.001,1.141,0.46,2.187,1.207,2.934c0.748,0.749,1.78,1.222,2.92,1.222h14.453V41.27 c0,1.142,0.453,2.176,1.201,2.922c0.748,0.748,1.777,1.211,2.919,1.211c2.282,0,4.129-1.851,4.129-4.133V26.857h14.435 c2.283,0,4.134-1.867,4.133-4.15C45.399,20.425,43.548,18.557,41.267,18.557z"></path> </g> </g></svg>
        &nbsp; Add Section</x-secondary-button>
        @if (session('status') === 'section-created')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Section created successfully.') }}</p>
            @endif
    </header>
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
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Description')" />
            <x-textarea-input id="description" name="description" type="text"
                class="mt-1 block w-full" required autofocus
                autocomplete="description">{{ old('description') }}</x-textarea-input>
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