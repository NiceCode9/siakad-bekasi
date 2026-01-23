@extends('layouts.app')

@section('title', 'Manajemen Role')

@push('styles')
    <style>
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .form-group label {
            font-weight: 600;
        }

        .checkbox-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            background: #f8f9fa;
        }

        .permission-group {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }

        .permission-group-title {
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            color: #495057;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #007bff;
        }

        .checkbox-item {
            padding: 5px 0;
        }

        .select-all-group {
            margin-bottom: 10px;
            padding: 8px;
            background: #e7f3ff;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Manajemen Role</h1>
                <div class="top-right-button-container">
                    <button class="btn btn-primary btn-sm" id="btn-add">
                        <i class="simple-icon-plus"></i> Tambah Role
                    </button>
                </div>
                <div class="separator mb-5"></div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="text-nowrap" id="roleTable">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Role</th>
                                        <th>Guard</th>
                                        <th>Jumlah Permission</th>
                                        <th>Jumlah User</th>
                                        <th>Permissions</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="roleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Role</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="roleForm">
                    <div class="modal-body">
                        <input type="hidden" id="role_id" name="role_id">

                        <div class="form-group">
                            <label for="name">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <small class="form-text text-muted">
                                Contoh: admin, guru, siswa, kepala-sekolah
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="guard_name">Guard Name</label>
                            <select class="form-control" id="guard_name" name="guard_name">
                                <option value="web">web</option>
                                <option value="api">api</option>
                            </select>
                            <small class="form-text text-muted">
                                Default: web (untuk aplikasi web biasa)
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Assign Permission -->
    <div class="modal fade" id="permissionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Permission ke Role</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="permissionForm">
                    <div class="modal-body">
                        <input type="hidden" id="permission_role_id" name="role_id">

                        <div class="alert alert-info">
                            <strong>Role:</strong> <span id="permission_role_name"></span>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-success" id="btn-select-all">
                                <i class="simple-icon-check"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" id="btn-deselect-all">
                                <i class="simple-icon-close"></i> Hapus Semua
                            </button>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-list" id="permissionsList">
                                <!-- Will be populated via AJAX -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // DataTable
            var table = $('#roleTable').DataTable({
                sDom: '<"row view-filter"<"col-sm-12"<"float-right"l><"float-left"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.role.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'guard',
                        name: 'guard_name'
                    },
                    {
                        data: 'permissions_count',
                        name: 'permissions_count'
                    },
                    {
                        data: 'users_count',
                        name: 'users_count'
                    },
                    {
                        data: 'permissions_list',
                        name: 'permissions_list',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                drawCallback: function() {
                    $($(".dataTables_wrapper .pagination li:first-of-type"))
                        .find("a")
                        .addClass("prev");
                    $($(".dataTables_wrapper .pagination li:last-of-type"))
                        .find("a")
                        .addClass("next");

                    $(".dataTables_wrapper .pagination").addClass("pagination-sm");
                },
                language: {
                    paginate: {
                        previous: "<i class='simple-icon-arrow-left'></i>",
                        next: "<i class='simple-icon-arrow-right'></i>"
                    },
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Items Per Page _MENU_"
                },
            });

            // Add Button
            $('#btn-add').click(function() {
                $('#roleForm')[0].reset();
                $('#role_id').val('');
                $('#modalTitle').text('Tambah Role');
                $('#guard_name').val('web');
                $('#roleModal').modal('show');
            });

            // Edit Button
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/role') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        $('#role_id').val(response.data.id);
                        $('#name').val(response.data.name);
                        $('#guard_name').val(response.data.guard_name);

                        $('#modalTitle').text('Edit Role');
                        $('#roleModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal memuat data role', 'error');
                    }
                });
            });

            // Submit Form
            $('#roleForm').submit(function(e) {
                e.preventDefault();

                var id = $('#role_id').val();
                var url = id ? "{{ url('admin/role') }}/" + id : "{{ route('admin.role.store') }}";
                var type = id ? 'PUT' : 'POST';

                var formData = {
                    name: $('#name').val(),
                    guard_name: $('#guard_name').val()
                };

                $('#btn-save').prop('disabled', true).html(
                    '<i class="simple-icon-refresh"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    success: function(response) {
                        $('#roleModal').modal('hide');
                        table.ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON?.errors;
                        var errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan';

                        if (errors) {
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: errorMessage
                        });
                    },
                    complete: function() {
                        $('#btn-save').prop('disabled', false).html('Simpan');
                    }
                });
            });

            // Delete Button
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data role akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/role') }}/" + id,
                            type: 'DELETE',
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: xhr.responseJSON?.message ||
                                        'Gagal menghapus role'
                                });
                            }
                        });
                    }
                });
            });

            // Assign Permission Button
            $(document).on('click', '.btn-assign-permission', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/role') }}/" + id + "/permissions",
                    type: 'GET',
                    success: function(response) {
                        $('#permission_role_id').val(id);
                        $('#permission_role_name').text(response.data.role.name);

                        var permissionsList = '';

                        // Loop through grouped permissions
                        $.each(response.data.all_permissions, function(groupName, permissions) {
                            permissionsList += `
                        <div class="permission-group">
                            <div class="permission-group-title">
                                <i class="simple-icon-folder"></i> ${groupName.toUpperCase()}
                            </div>
                            <div class="select-all-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input select-group"
                                           id="select_${groupName}" data-group="${groupName}">
                                    <label class="custom-control-label font-weight-bold" for="select_${groupName}">
                                        Pilih Semua ${groupName}
                                    </label>
                                </div>
                            </div>
                    `;

                            $.each(permissions, function(index, permission) {
                                var checked = response.data.assigned_permissions
                                    .includes(permission.id) ? 'checked' : '';
                                permissionsList += `
                            <div class="checkbox-item">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input permission-checkbox"
                                           id="permission_${permission.id}"
                                           name="permissions[]" value="${permission.id}"
                                           data-group="${groupName}" ${checked}>
                                    <label class="custom-control-label" for="permission_${permission.id}">
                                        ${permission.name}
                                    </label>
                                </div>
                            </div>
                        `;
                            });

                            permissionsList += `</div>`;
                        });

                        $('#permissionsList').html(permissionsList);
                        $('#permissionModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal memuat data permission', 'error');
                    }
                });
            });

            // Select All Permissions
            $(document).on('click', '#btn-select-all', function() {
                $('.permission-checkbox').prop('checked', true);
                $('.select-group').prop('checked', true);
            });

            // Deselect All Permissions
            $(document).on('click', '#btn-deselect-all', function() {
                $('.permission-checkbox').prop('checked', false);
                $('.select-group').prop('checked', false);
            });

            // Select All in Group
            $(document).on('change', '.select-group', function() {
                var group = $(this).data('group');
                var isChecked = $(this).is(':checked');

                $('.permission-checkbox[data-group="' + group + '"]').prop('checked', isChecked);
            });

            // Update group checkbox when individual permission changes
            $(document).on('change', '.permission-checkbox', function() {
                var group = $(this).data('group');
                var totalInGroup = $('.permission-checkbox[data-group="' + group + '"]').length;
                var checkedInGroup = $('.permission-checkbox[data-group="' + group + '"]:checked').length;

                $('#select_' + group).prop('checked', totalInGroup === checkedInGroup);
            });

            // Submit Permission Form
            $('#permissionForm').submit(function(e) {
                e.preventDefault();

                var id = $('#permission_role_id').val();
                var selectedPermissions = [];

                $('input[name="permissions[]"]:checked').each(function() {
                    selectedPermissions.push($(this).val());
                });

                $.ajax({
                    url: "{{ url('admin/role') }}/" + id + "/permissions",
                    type: 'POST',
                    data: {
                        permissions: selectedPermissions
                    },
                    success: function(response) {
                        $('#permissionModal').modal('hide');
                        table.ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message ||
                            'Gagal assign permission', 'error');
                    }
                });
            });
        });
    </script>
@endpush
