<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-sm">
                    <x-primary-link href="/create-form"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-4 h-4 rounded bg-white"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/></svg>
                        &nbsp; Tambah Form</x-primary-link>
                    <table id="dashboardTable">
                        <thead>
                            <tr>
                                <th>Nama Tabel</th>
                                <th>Slug</th>
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
                            let link =
                                `<a type="button" class="btn btn-icon btn-info waves-effect waves-light" style="margin:5px;" href="{{ url('/pengajuan') }}/` +
                                row.id +
                                `" title="Lihat">
                                <i class="fa fa-search"></i></a>&nbsp;
                                `;

                            return link;
                        }
                    },

                ]
            });
        });
    </script>
</x-app-layout>
