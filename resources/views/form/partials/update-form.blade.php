<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Form') }}
        </h2>
    </header>

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

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>