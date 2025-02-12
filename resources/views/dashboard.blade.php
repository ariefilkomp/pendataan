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
                    <table id="dashboardTable">
                        <thead>
                            <tr>
                                @if(auth()->user()->hasAnyRole(['admin', 'opd']))
                                <th>Nama Tabel</th>
                                <th>Slug</th>
                                @endif
                                <th>Nama Form</th>
                                <th>Deskripsi</th>
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
                    url: "{{ url('/dashboard') }}",
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
                            let editLink = '';
                            let statLink = '';
                            let fillLink = '';
                            @role('admin')
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
                            @endrole

                            @role('opd')
                                statLink =
                                    `<a type="button" class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50" style="margin:5px;" href="{{ url('/data') }}/` +
                                    row.id +
                                    `" title="Lihat">Statistik</a>&nbsp;
                                    `;
                            @endrole

                            fillLink = `<a type="button" class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50" style="margin:5px;" href="{{ url('/') }}/` +
                                row.slug + `" title="Isi Data">Isi Data</a>&nbsp;`;

                            let link = `<div class="flex justify-center gap-4">` + editLink + statLink + fillLink + `</div>`;

                            return link;
                        }
                    },

                ]
            });
        });
    </script>
</x-app-layout>
