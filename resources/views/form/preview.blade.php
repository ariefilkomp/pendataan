<x-common-layout>
    @push('head')
        <meta name="description" content="{{ $form?->description }}" itemprop="description" />
        <meta name="title" content="{{ $form?->name }}" />
        <meta name="originalTitle" content="{{ $form?->name }}" />
    @endpush
    <section class="relative">
        <div class="relative pt-24 lg:pt-28">
            <div class="mx-auto px-6 max-w-7xl md:px-12">
                <h1 class="text-wrap mx-auto mt-8 max-w-xl text-xl md:text-2xl text-slate-300 bg-gray-100 p-2">
                    PREVIEW
                </h1>
                <h1 class="text-wrap mx-auto mt-8 max-w-xl text-3xl md:text-4xl font-semibold text-body">
                    {{ $form?->name }}
                </h1>
                <h3 class="text-wrap mx-auto mt-8 max-w-xl text-lg text-body">{{ $section->description }}</h3>
                    @if($form->sections->count() == 1)
                        @foreach($form->questions->sortBy('created_at') as $question)
                            <x-question :question="$question" :answers="$data"></x-question>
                        @endforeach
                        <div class="p-4 max-w-xl m-auto flex justify-end">
                            <x-primary-button type="submit" class="mt-4" disabled>
                                {{ __('Submit') }}
                            </x-primary-button>
                        </div>
                    @else
                        @foreach($form->questions->where('section_id', $section->id)->sortBy('created_at') as $question)
                            @if(empty($question)) @continue @endif
                            <x-question :question="$question" :answers="$data"></x-question>
                        @endforeach
                        @php 
                            $classStyle = $section->order == 1 ? "justify-end" : "justify-between";
                            $backSectionId = $section->order > 0 ? $form->sections->where('order', $section->order - 1)->first()?->id : '#';
                            $nextStr = $form->sections->where('order', $section->order)->first()?->order == $form->sections->count() ? 'Submit' : 'Next >';
                            if($nextStr == 'Next >') {
                                $nextSectionId = $form->sections->where('order', $section->order + 1)->first()?->id;
                            }
                        @endphp
                        <div class="p-4 max-w-xl m-auto flex {{$classStyle}}">
                            @if($section->order > 1)
                                <x-secondary-link href="{{ url('preview/'.$form->slug.'/'.$backSectionId) }}" class="mt-4">
                                    {{ __('< Back') }}
                                </x-secondary-link>
                            @endif
                            @if($nextStr == 'Next >') {
                                <x-primary-link href="{{ url('preview/'.$form->slug.'/'.$nextSectionId) }}" class="mt-4">
                                    {{ __($nextStr) }}
                                </x-primary-link>
                            @else
                                <x-primary-link href="#" class="mt-4">
                                    {{ __($nextStr) }}
                                </x-primary-link>
                            @endif
                        </div>
                    @endif

            </div>
        </div>
    </section>
</x-common-layout>