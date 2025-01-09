<x-app-layout>
    <x-slot name="header">
        <section id="page-title">

            <div class="container clearfix">
                <h1 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Users') }}
                </h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </div>

        </section>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                @if (session('status') === 'role-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-2xl font-bold text-green-600 dark:text-green-400"
                >{{ __('Role Updated.') }}</p>
            @endif
                <table id="tblUser" class="table table-striped">
                    <thead>
                        <tr style="text-align:center;" role="row">
                            <th>Nama </th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <x-modal name="edit-role-modal" :show="$errors->updateRole->isNotEmpty()" focusable>
        <form method="post" action="{{ route('edit-role') }}" class="p-6">
            @method('patch')
            @csrf
            <input type="hidden" name="id" value="" id="user_id_update">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Edit Role') }}
            </h2>
    
            <div class="mt-4">
                <x-input-label for="role" :value="__('Role')" />
                <x-select-input name="role" id="role_update" class="mt-1 block w-full">
                    <option value="umum" {{ old('type') == 'umum' ? 'selected' : '' }}>UMUM</option>
                    <option value="opd" {{ old('type') == 'opd' ? 'selected' : '' }}>OPD</option>
                    <option value="admin" {{ old('type') == 'admin' ? 'selected' : '' }}>ADMIN</option>
                </x-select-input>
                <x-input-error class="mt-2" :messages="$errors->get('type')" />
            </div>
    
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
    
                <x-primary-button class="ms-3">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <script>
        $(document).ready(function() {

            var tblUser = $('#tblUser').dataTable({
                "processing": true,
                "serverSide": true,
                "searchDelay": 350,
                "ajax": {
                    url: "{{ route('admin.users.table') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}"
                    }
                },
                "columnDefs": [],
                "drawCallback": function() {
                    $('#tblUser_filter').removeClass('pull-right').addClass('pull-right');
                },
                "columns": [{
                        "render": function(data, type, row, meta) {
                            return row.name;
                        }
                    },
                    {
                        "render": function(data, type, row, meta) {
                            return row.email;
                        }
                    },
                    {
                        "render": function(data, type, row, meta) {
                            return row.roles[0].name;
                        }
                    },
                    {
                        "render": function(data, type, row, meta) {
                            let link =
                                `<a href="javascript:void(0)" x-on:click.prevent="$dispatch('open-modal', 'edit-role-modal') " onclick="setUpdateData('${row.id}', '${row.roles[0].name}')" class="rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">Edit Role</a>
                            `;
                            return link;
                        }
                    },

                ]
            });

        });

        function setUpdateData(id, role) {
            $('#user_id_update').val(id);
            $('#role_update').val(role);
        }
    </script>

</x-app-layout>
