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
