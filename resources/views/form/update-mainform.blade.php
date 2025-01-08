<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Form') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-2xl m-auto">
                    @include('form.partials.update-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="mt-6 max-w-4xl mx-auto">
                    <ul
                        class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">
                        @foreach ($form->sections->sortBy('order', SORT_NUMERIC) as $section)
                            @if ($section->id == $section_id)
                                <li class="me-2">
                                    <a href="#" aria-current="page"
                                        class="inline-block p-4 text-blue-600 bg-gray-100 rounded-t-lg active dark:bg-gray-800 dark:text-blue-500">
                                        {{ $section->name }}
                                    </a>
                                </li>
                            @else
                                <li class="me-2">
                                    <a href="{{ url('/edit-form/' . $form->id . '/' . $section->id) }}"
                                        class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                                        {{ $section->name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                        <li class="me-2">
                            <a href="#" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'add-section-modal')"
                                class="flex p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                                <svg class="w-4 h-4" fill="#000000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 45.402 45.402" xml:space="preserve">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <g>
                                            <path
                                                d="M41.267,18.557H26.832V4.134C26.832,1.851,24.99,0,22.707,0c-2.283,0-4.124,1.851-4.124,4.135v14.432H4.141 c-2.283,0-4.139,1.851-4.138,4.135c-0.001,1.141,0.46,2.187,1.207,2.934c0.748,0.749,1.78,1.222,2.92,1.222h14.453V41.27 c0,1.142,0.453,2.176,1.201,2.922c0.748,0.748,1.777,1.211,2.919,1.211c2.282,0,4.129-1.851,4.129-4.133V26.857h14.435 c2.283,0,4.134-1.867,4.133-4.15C45.399,20.425,43.548,18.557,41.267,18.557z">
                                            </path>
                                        </g>
                                    </g>
                                </svg>
                                &nbsp; Add Section
                            </a>
                        </li>
                    </ul>
                    @if($form->sections->count() > 0)
                    <div class="flex justify-between">
                        <div class="flex flex-col">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-4">
                                {{ __('Section ' . $form->sections?->where('id', $section_id)->first()?->order . ' / ' . $form->sections->count()) }}
                            </h2>
                            <x-secondary-button x-data="" x-on:click="$dispatch('open-modal', 'edit-section-modal')" >
                                Edit Section
                            </x-secondary-button>
                        </div>
                        <p class="text-gray-500 text-xs text-right mt-4">
                            <button x-data="" x-on:click="$dispatch('open-modal', 'delete-section-modal')"
                                class="right-0 inline-flex items-center px-2 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Delete
                                Section</button>
                        </p>
                    </div>
                    @endif

                </div>

                <div class="mt-6 max-w-2xl m-auto">
                    @include('form.partials.update-question')
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
