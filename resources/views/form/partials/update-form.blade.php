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
            <form method="post" action="{{ url('edit-form') }}" class="mt-6 space-y-6">
                @csrf
                @method('patch')
                <input type="hidden" name="id" value="{{ $form->id }}">
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
                    <x-input-label for="for_role" :value="__('For Role')" />
                    <x-select-input name="for_role" id="for_role" class="mt-1 block w-full">
                        <option value="opd" {{ old('for_role',$form->for_role) == 'opd' ? 'selected' : '' }}>OPD</option>
                        <option value="umum" {{ old('for_role',$form->for_role) == 'umum' ? 'selected' : '' }}>UMUM</option>
                    </x-select-input>
                    <x-input-error class="mt-2" :messages="$errors->get('for_role')" />
                </div>

                <div>
                    <x-input-label for="multi_entry" :value="__('Jika Multi Entry dipilih, User dapat input lebih dari 1 kali.')" class="text-xs"/>
                    <label class="cursor-pointer">
                        <span class="block font-medium text-sm text-gray-700 dark:text-gray-300">Multi Entry</span>
                        <input type="checkbox" id="multi_entry" value="1"
                            name="multi_entry" class="sr-only peer" @if($form->multi_entry) checked @endif>
                        <div
                            class="mt-2 relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                        </div>
                    </label>
                    <x-input-error class="mt-2" :messages="$errors->get('multi_entry')" />
                </div>

                <div>
                    <label class="cursor-pointer">
                        <span class="block font-medium text-sm text-gray-700 dark:text-gray-300">Published</span>
                        <input type="checkbox" id="published" value="1"
                            name="published" class="sr-only peer" @if($form->published) checked @endif>
                        <div
                            class="mt-2 relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                        </div>
                    </label>
                    <x-input-error class="mt-2" :messages="$errors->get('published')" />
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
                    <div class="relative">
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full"
                            :value="old('slug', $form->slug)" autocomplete="slug"
                            placeholder="jika tidak diisi maka slug akan berupa uuid" />
                        <button type="button" onclick="copyToClipboard('{{ url($form->slug) }}')" class="absolute end-2 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg p-2 inline-flex items-center justify-center">
                            <span id="default-icon">
                                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                    @if($form->slug)
                        <a href="{{ url($form->slug) }}" target="_blank" class="text-blue-500 dark:text-blue-400">{{ url($form->slug) }}</a>
                    @endif
                </div>

                <div>
                    <x-input-label for="short_slug" :value="__('Short Url Slug')" />
                    <div class="relative">
                        <x-text-input id="short_slug" name="short_slug" type="text" class="mt-1 block w-full" :value="old('short_slug', $form->short_slug)" required autofocus autocomplete="short_url" disabled readonly/>
                        <button type="button" onclick="copyToClipboard('https://s.id/{{ $form->short_slug }}')" class="absolute end-2 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg p-2 inline-flex items-center justify-center">
                            <span id="default-icon">
                                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('short_slug')" />
                    @if($form->short_slug)
                        <a href="https://s.id/{{ $form->short_slug }}" target="_blank" class="text-blue-500 dark:text-blue-400">https://s.id/{{ $form->short_slug }}</a>
                    @endif
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
<script>
    function copyToClipboard(textToCopy) {
        const textarea = document.createElement('textarea');
        textarea.value = textToCopy;
        // Add the textarea to the page
        document.body.appendChild(textarea);

        // Select the text in the textarea
        textarea.select();

        // Copy the text
        document.execCommand('copy');

        // Remove the textarea from the page
        document.body.removeChild(textarea);
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: textToCopy + ' Copied to clipboard',
            showConfirmButton: false,
            timer: 1500
        })

    }
</script>