<x-common-layout>
    @push('head')
        <meta name="description" content="{{ $form?->description }}" itemprop="description" />
        <meta name="title" content="{{ $form?->name }}" />
        <meta name="originalTitle" content="{{ $form?->name }}" />
    @endpush
    <div class="p-4 my-20 max-w-xl m-auto">
        <div class="p-4 text-2xl font-bold border border-gray-200 sm:p-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg">
            Anda Harus <a href="{{ route('login') }}" class="text-blue-500">Login</a> terlebih dahulu untuk mengisi form ini.
        </div>
    </div>
</x-common-layout>