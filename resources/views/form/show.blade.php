<x-common-layout>
    
    <section class="relative">
        <div class="relative pt-24 lg:pt-28">
            <div class="mx-auto px-6 max-w-7xl md:px-12">
                <h1 class="text-wrap mx-auto mt-8 max-w-xl text-3xl md:text-4xl font-semibold text-body">
                    {{ $form?->name }}
                </h1>
                <h3 class="text-wrap mx-auto mt-8 max-w-xl text-lg text-body">{{ $section->description }}</h3>
                <form method="post" action="{{ route('form-submit') }}" class="mt-6 space-y-6">
                    @csrf
                    <input type="hidden" name="id" value="{{ $form->id }}">
                    <input type="hidden" name="section_id" value="{{ $section->id }}">
                    @if($form->sections->count() == 1)
                        @foreach($form->questions->sortBy('created_at') as $question)
                            <x-question :question="$question" :answers="$data"></x-question>
                        @endforeach
                        <div class="p-4 max-w-xl m-auto flex justify-end">
                            <x-primary-button type="submit" class="mt-4">
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
                        @endphp
                        <div class="p-4 max-w-xl m-auto flex {{$classStyle}}">
                            @if($section->order > 1)
                                <x-secondary-link href="{{ url($form->slug.'/'.$backSectionId) }}" class="mt-4">
                                    {{ __('< Back') }}
                                </x-secondary-link>
                            @endif
                            <x-primary-button type="submit" class="mt-4">
                                {{ __('Next >') }}
                            </x-primary-button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </section>
    <script>
        function saveFile(fieldId, questionId, answerId) {
            var formData = new FormData();
            let file = $("#" + fieldId)[0].files[0];
            if (file == undefined) return;
            urlx = '{{ route('upload-file') }}';

            if (file.size > 1000000) {
                Swal.fire({
                    title: ':(',
                    text: 'Ukuran file Tidak boleh lebih dari 1 MB.',
                    icon: 'error',
                    confirmButtonText: 'close'
                });
                return;
            }

            formData.append('file', file);
            formData.append('form_id', '{{ $form->id }}');
            formData.append('question_id', questionId);
            formData.append('answer_id', answerId);
            formData.append('_token', '{{ csrf_token() }}');
            
            $.ajax({
                type: "POST",
                url: urlx,
                data: formData,
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                beforeSend: function() {
                    Swal.fire({
                        text: 'Menyimpan ...',
                    });
                    Swal.showLoading();
                },
                success: function(result) {
                    console.log(result);
                    if (result.success === true) {
                        Swal.fire({
                            text: result.message,
                            icon: 'success',
                            confirmButtonText: 'close',
                        });
                    } else if (result.success === false) {
                        Swal.fire({
                            text: result.message,
                            icon: 'error',
                            confirmButtonText: 'close'
                        });
                    } else {
                        console.log('parse error!');
                    }
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentVal = percentComplete + '%';
                    console.log(percentVal);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    let allMessage = '<ul>';
                    let title =  jqXHR.responseJSON?.message;
                    if(jqXHR.responseJSON?.errors) {
                        $.each(jqXHR.responseJSON?.errors, function(key, value) {
                            allMessage += '<li class="alert alert-danger">' + value + '</li>';
                        });
                    } else {
                        allMessage += '<li class="alert alert-danger">SERVER ERROR!</li>'
                    }
                    allMessage += '</ul>';

                    Swal.fire({
                        title: title,
                        html: allMessage,
                        icon: 'error',
                        confirmButtonText: 'close',
                    });
                }
            });
        }
    </script>
</x-common-layout>