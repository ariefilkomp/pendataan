<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Form') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Create Form') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Untuk membuat form, silahkan isi form dibawah.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('create-form') }}" class="mt-6 space-y-6">
                            @csrf
                            <div>
                                <x-input-label for="name" :value="__('Nama Form *')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    :value="old('name')" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="description" :value="__('Deskripsi Form *')" />
                                <x-textarea-input id="description" name="description" type="text" class="mt-1 block w-full"
                                    required autofocus autocomplete="description" >{{old('description')}}</x-textarea-input>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            <div>
                                <x-input-label for="for_role" :value="__('For Role')" />
                                <x-select-input name="for_role" id="for_role" class="mt-1 block w-full">
                                    <option value="opd" {{ old('for_role') == 'opd' ? 'selected' : '' }}>OPD</option>
                                    <option value="umum" {{ old('for_role') == 'umum' ? 'selected' : '' }}>UMUM</option>
                                </x-select-input>
                                <x-input-error class="mt-2" :messages="$errors->get('for_role')" />
                            </div>

                            <div>
                                <x-input-label for="multi_entry" :value="__('Jika Multi Entry dipilih, User dapat input lebih dari 1 kali.')" class="text-xs"/>
                                <label class="cursor-pointer">
                                    <span class="block font-medium text-sm text-gray-700 dark:text-gray-300">Multi Entry</span>
                                    <input type="checkbox" id="multi_entry" value="1"
                                        name="multi_entry" class="sr-only peer" checked>
                                    <div
                                        class="mt-2 relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>
                                <x-input-error class="mt-2" :messages="$errors->get('multi_entry')" />
                            </div>

                            @role('admin')
                            <div>
                                <label class="cursor-pointer">
                                    <span class="block font-medium text-sm text-gray-700 dark:text-gray-300">Published</span>
                                    <input type="checkbox" id="published" value="1"
                                        name="published" class="sr-only peer">
                                    <div
                                        class="mt-2 relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>
                                <x-input-error class="mt-2" :messages="$errors->get('published')" />
                            </div>
                            @endrole

                            <div>
                                <x-input-label for="table_name" :value="__('Nama Tabel, format Alphanumeric underscore. contoh : tabel_saya')" />
                                <x-text-input id="table_name" name="table_name" type="text" class="mt-1 block w-full"
                                    :value="old('table_name')" autocomplete="table_name" placeholder="jika tidak diisi maka nama table random"/>
                                <x-input-error class="mt-2" :messages="$errors->get('table_name')" />
                            </div>

                            <div>
                                <x-input-label for="slug" :value="__('Slug, adalah url form misal : nama-form-saya')" />
                                <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full"
                                    :value="old('slug')" autocomplete="slug" placeholder="jika tidak diisi maka slug akan berupa uuid" />
                                <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                            </div>

                            <div>
                                <label class="cursor-pointer">
                                    <span class="block font-medium text-sm text-gray-700 dark:text-gray-300">Auto create short url</span>
                                    <input type="checkbox" id="short_url" value="1"
                                        name="short_url" class="sr-only peer">
                                    <div
                                        class="mt-2 relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>
                                <x-input-error class="mt-2" :messages="$errors->get('short_url')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
