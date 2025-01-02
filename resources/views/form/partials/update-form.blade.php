<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Form') }}
        </h2>
    </header>

    <div x-data="{ isExpanded: false }" class="divide-y divide-neutral-300 dark:divide-neutral-700">
        <button id="controlsAccordionItemOne" type="button" class="flex w-full items-center justify-between gap-4 bg-neutral-50 p-4 text-left underline-offset-2 hover:bg-neutral-50/75 focus-visible:bg-neutral-50/75 focus-visible:underline focus-visible:outline-none dark:bg-neutral-900 dark:hover:bg-neutral-900/75 dark:focus-visible:bg-neutral-900/75" aria-controls="accordionItemOne" @click="isExpanded = ! isExpanded" :class="isExpanded ? 'text-onSurfaceStrong dark:text-onSurfaceDarkStrong font-bold'  : 'text-onSurface dark:text-onSurfaceDark font-medium'" :aria-expanded="isExpanded ? 'true' : 'false'">
            Update form : {{ $form->name ?? ''}}
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor" class="size-5 shrink-0 transition" aria-hidden="true" :class="isExpanded  ?  'rotate-180'  :  ''">
               <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>
        <div x-cloak x-show="isExpanded" id="accordionItemOne" role="region" aria-labelledby="controlsAccordionItemOne" x-collapse>
            <form method="post" action="{{ route('create-form') }}" class="mt-6 space-y-6">
                @csrf
                <div>
                    <x-input-label for="name" :value="__('Nama Form *')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                        :value="old('name', $form->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
        
                <div>
                    <x-input-label for="description" :value="__('Deskripsi Form *')" />
                    <x-textarea-input id="description" name="description" type="text"
                        class="mt-1 block w-full" required autofocus
                        autocomplete="description">{{ old('description', $form->description) }}</x-textarea-input>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>
        
                <div>
                    <x-input-label for="table_name" :value="__('Nama Tabel, format Alphanumeric underscore. contoh : tabel_saya')" />
                    <x-text-input id="table_name" name="table_name" type="text" class="mt-1 block w-full"
                        :value="old('table_name', $form->table_name)" autocomplete="table_name"
                        placeholder="jika tidak diisi maka nama table random" />
                    <x-input-error class="mt-2" :messages="$errors->get('table_name')" />
                </div>
        
                <div>
                    <x-input-label for="slug" :value="__('Slug, adalah url form misal : nama-form-saya')" />
                    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full"
                        :value="old('slug', $form->slug)" autocomplete="slug"
                        placeholder="jika tidak diisi maka slug akan berupa uuid" />
                    <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                </div>

                <div id="add-question">

                </div>
        
                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
        
                    @if (session('status') === 'profile-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>