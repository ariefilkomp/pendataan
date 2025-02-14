<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <h2 class=" px-6 py-4 font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Available Form') }}
                </h2>
                <div class="p-6 text-gray-900 dark:text-gray-100 text-sm">
                    @if(auth()->user()->hasAnyRole(['admin', 'opd']))
                        <x-primary-link href="/create-form"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-4 h-4 rounded bg-white"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/></svg>
                            &nbsp; Tambah Form</x-primary-link>
                    @endif
                    <table id="dashboardTable">
                        <thead>
                            <tr>
                                @if(auth()->user()->hasAnyRole(['admin', 'opd']))
                                <th>Nama Tabel</th>
                                <th>Slug</th>
                                @endif
                                <th>Nama Form</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Dipublish</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="module">
        $( document ).ready(function() {
            // $('#dashboardTable').DataTable();
            $('#dashboardTable').dataTable({
                "processing": true,
                "serverSide": true,
                "searchDelay": 350,
                "ajax": {
                    url: "{{ url('/forms') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}"
                    }
                },
                "columnDefs": [],
                "columns": [
                    @if(auth()->user()->hasAnyRole(['admin', 'opd']))
                    {
                        "render": function(data, type, row, meta) {
                            return row.table_name;
                        }
                    },
                    {
                        "render": function(data, type, row, meta) {
                            return row.slug;
                        }
                    },
                    @endif
                    {
                        "render": function(data, type, row, meta) {
                            return row.name;
                        }
                    },
                    {
                        "render": function(data, type, row, meta) {
                            return row.description;
                        }
                    },
                    {
                        "render": function(data, type, row, meta) {
                            return row.status;
                        }
                    },
                    {
                        "render": function(data, type, row, meta) {
                            return row.published == 1 ? 'Ya' : 'Tidak';
                        }
                    },
                    {
                        "render": function(data, type, row, meta) {
                            let editLink = '';
                            let statLink = '';
                            let fillLink = '';
                            let previewLink = '';
                            
                            editLink =
                                `<a type="button" class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50" style="margin:5px;" href="{{ url('/edit-form') }}/` +
                                row.id +
                                `" title="Lihat">Edit</a>&nbsp;
                                `;
                            statLink =
                                `<a type="button" class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50" style="margin:5px;" href="{{ url('/data') }}/` +
                                row.id +
                                `" title="Lihat">Data</a>&nbsp;
                                `;

                            previewLink = `<a type="button" class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50" style="margin:5px;" href="{{ url('/') }}/preview/` +
                                row.slug + `" title="Isi Data">Preview</a>&nbsp;`;

                            fillLink = `<a type="button" class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50" style="margin:5px;" href="{{ url('/') }}/` +
                                row.slug + `" title="Isi Data">Isi Data</a>&nbsp;`;

                            let link = `<div class="flex flex-wrap justify-center w-[150px]">` + editLink + statLink + fillLink + previewLink + `</div>`;

                            return link;
                        }
                    },

                ]
            });
        });
    </script>
</x-app-layout>
