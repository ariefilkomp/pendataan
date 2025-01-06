<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Stats') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <table id="statTable">
                    <thead>
                        <tr>
                            <th>Submitter</th>
                            @foreach($form->questions->sortBy('created_at') as $question)
                                <th>{{ $question->question }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $( document ).ready(function() {
            $('#statTable').dataTable({
                "processing": true,
                "serverSide": true,
                "searchDelay": 350,
                "ajax": {
                    url: "{{ url('/stat-table') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}",
                        d.id = "{{ $form->id }}"
                    }
                },
                "columnDefs": [],
                "columns": [
                    {
                        "render": function(data, type, row, meta) {
                            return row.user_name;
                        }
                    },
                    @foreach($form->questions->sortBy('created_at') as $question)
                        @if($question->type == 'checkboxes')
                            {
                                "render": function(data, type, row, meta) {
                                    if(row.{{ $question->column_name }} == null) {
                                        return '';
                                    } else {
                                       let opt = JSON.parse(row.{{ $question->column_name }});
                                       let ul = `<ul>`;
                                       for (let i = 0; i < opt.length; i++) {
                                           ul += `<li>${opt[i]}</li>`;
                                       }
                                       ul += `</ul>`;
                                       return ul;
                                    }
                                }
                            },
                        @elseif($question->type == 'file')
                            {
                                "render": function(data, type, row, meta) {
                                    if(row.{{ $question->column_name }} == null) {
                                        return '';
                                    } else {
                                       let opt = JSON.parse(row.{{ $question->column_name }});
                                       let ul = `<ul>`;
                                       for (let i = 0; i < opt.length; i++) {
                                           ul += `<li><a href="{{url('/storage/'.$form->id)}}/${opt[i]}" target="_blank">file ke-${i+1}</a></li>`;
                                       }
                                       ul += `</ul>`;
                                       return ul;
                                    }
                                }
                            },
                        @else
                            {
                                "render": function(data, type, row, meta) {
                                    return row.{{ $question->column_name }};
                                }
                            },
                        @endif
                    @endforeach
                ]
            });
        });
    </script>
</x-app-layout>
