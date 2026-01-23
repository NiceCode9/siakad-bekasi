@extends('layouts.app')

@section('title', 'Manajemen Permission')

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
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            background: #f8f9fa;
        }

        .checkbox-item {
            padding: 5px 0;
        }

        .action-tag {
            display: inline-block;
            padding: 4px 8px;
            margin: 2px;
            background: #e9ecef;
            border-radius: 3px;
            font-size: 12px;
        }

        .action-tag .remove-tag {
            margin-left: 5px;
            cursor: pointer;
            color: #dc3545;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Manajemen Permission</h1>
                <div class="top-right-button-container">
                    <button class="btn btn-success btn-sm" id="btn-bulk-create">
                        <i class="simple-icon-layers"></i> Bulk Create
                    </button>
                    <button class="btn btn-primary btn-sm" id="btn-add">
                        <i class="simple-icon-plus"></i> Tambah Permission
                    </button>
                </div>
                <div class="separator mb-5"></div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="text-nowrap" id="permissionTable">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Permission</th>
                                        <th>Kategori</th>
                                        <th>Guard</th>
                                        <th>Jumlah Role</th>
                                        <th>Jumlah User</th>
                                        <th>Roles</th>
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
    <div class="modal fade" id="permissionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Permission</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="permissionForm">
                    <div class="modal-body">
                        <input type="hidden" id="permission_id" name="permission_id">

                        <div class="form-group">
                            <label for="name">Nama Permission <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <small class="form-text text-muted">
                                Format: module-action (contoh: user-create, siswa-edit)
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

    <!-- Modal Bulk Create -->
    <div class="modal fade" id="bulkCreateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Create Permissions</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="bulkCreateForm">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Info:</strong> Fitur ini akan membuat multiple permissions sekaligus dengan format:
                            <code>prefix-action</code>
                        </div>

                        <div class="form-group">
                            <label for="prefix">Prefix/Module <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prefix" name="prefix" required
                                placeholder="user, siswa, guru, dll">
                            <small class="form-text text-muted">
                                Nama module/prefix untuk permissions (contoh: user, siswa, guru)
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Actions <span class="text-danger">*</span></label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="action_input"
                                    placeholder="list, create, edit, delete">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="btn-add-action">
                                        <i class="simple-icon-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted mb-2">
                                Masukkan action lalu klik Tambah. Contoh: list, create, edit, delete, view, export
                            </small>
                            <div id="actions-container" class="border rounded p-3 bg-light">
                                <div class="text-muted" id="no-actions">Belum ada action ditambahkan</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bulk_guard_name">Guard Name</label>
                            <select class="form-control" id="bulk_guard_name" name="guard_name">
                                <option value="web">web</option>
                                <option value="api">api</option>
                            </select>
                        </div>

                        <div class="alert alert-warning" id="preview-container" style="display: none;">
                            <strong>Preview Permissions:</strong>
                            <div id="preview-list" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btn-bulk-save">Buat Permissions</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Assign Role -->
    <div class="modal fade" id="roleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Permission ke Role</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="roleForm">
                    <div class="modal-body">
                        <input type="hidden" id="role_permission_id" name="permission_id">

                        <div class="alert alert-info">
                            <strong>Permission:</strong> <span id="role_permission_name"></span>
                        </div>

                        <div class="form-group">
                            <label>Pilih Role</label>
                            <div class="checkbox-list" id="rolesList">
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

            // Store actions for bulk create
            var bulkActions = [];

            // DataTable
            var table = $('#permissionTable').DataTable({
                sDom: '<"row view-filter"<"col-sm-12"<"float-right"l><"float-left"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.permission.index') }}",
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
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'guard',
                        name: 'guard_name'
                    },
                    {
                        data: 'roles_count',
                        name: 'roles_count'
                    },
                    {
                        data: 'users_count',
                        name: 'users_count'
                    },
                    {
                        data: 'roles_list',
                        name: 'roles_list',
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
                $('#permissionForm')[0].reset();
                $('#permission_id').val('');
                $('#modalTitle').text('Tambah Permission');
                $('#guard_name').val('web');
                $('#permissionModal').modal('show');
            });

            // Edit Button
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/permission') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        $('#permission_id').val(response.data.id);
                        $('#name').val(response.data.name);
                        $('#guard_name').val(response.data.guard_name);

                        $('#modalTitle').text('Edit Permission');
                        $('#permissionModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal memuat data permission', 'error');
                    }
                });
            });

            // Submit Form
            $('#permissionForm').submit(function(e) {
                e.preventDefault();

                var id = $('#permission_id').val();
                var url = id ? "{{ url('admin/permission') }}/" + id :
                    "{{ route('admin.permission.store') }}";
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
                    text: "Data permission akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/permission') }}/" + id,
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
                                        'Gagal menghapus permission'
                                });
                            }
                        });
                    }
                });
            });

            // Bulk Create Modal
            $('#btn-bulk-create').click(function() {
                $('#bulkCreateForm')[0].reset();
                bulkActions = [];
                updateActionsDisplay();
                updatePreview();
                $('#bulkCreateModal').modal('show');
            });

            // Add Action
            $('#btn-add-action').click(function() {
                var action = $('#action_input').val().trim().toLowerCase();

                if (!action) {
                    Swal.fire('Peringatan!', 'Masukkan nama action', 'warning');
                    return;
                }

                // Validate action name (only letters, numbers, dash, underscore)
                if (!/^[a-z0-9-_]+$/.test(action)) {
                    Swal.fire('Peringatan!', 'Action hanya boleh berisi huruf, angka, dash, dan underscore',
                        'warning');
                    return;
                }

                if (bulkActions.includes(action)) {
                    Swal.fire('Peringatan!', 'Action sudah ditambahkan', 'warning');
                    return;
                }

                bulkActions.push(action);
                $('#action_input').val('');
                updateActionsDisplay();
                updatePreview();
            });

            // Enter key to add action
            $('#action_input').keypress(function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#btn-add-action').click();
                }
            });

            // Remove Action
            $(document).on('click', '.remove-tag', function() {
                var action = $(this).data('action');
                bulkActions = bulkActions.filter(function(item) {
                    return item !== action;
                });
                updateActionsDisplay();
                updatePreview();
            });

            // Update preview when prefix changes
            $('#prefix').on('input', function() {
                updatePreview();
            });

            function updateActionsDisplay() {
                var container = $('#actions-container');

                if (bulkActions.length === 0) {
                    container.html('<div class="text-muted" id="no-actions">Belum ada action ditambahkan</div>');
                } else {
                    var html = '';
                    bulkActions.forEach(function(action) {
                        html += '<span class="action-tag">' + action +
                            '<span class="remove-tag" data-action="' + action + '">×</span></span>';
                    });
                    container.html(html);
                }
            }

            function updatePreview() {
                var prefix = $('#prefix').val().trim().toLowerCase();
                var previewContainer = $('#preview-container');
                var previewList = $('#preview-list');

                if (prefix && bulkActions.length > 0) {
                    var html = '<ul class="mb-0">';
                    bulkActions.forEach(function(action) {
                        html += '<li><code>' + prefix + '-' + action + '</code></li>';
                    });
                    html += '</ul>';

                    previewList.html(html);
                    previewContainer.show();
                } else {
                    previewContainer.hide();
                }
            }

            // Submit Bulk Create
            $('#bulkCreateForm').submit(function(e) {
                e.preventDefault();

                var prefix = $('#prefix').val().trim().toLowerCase();
                var guardName = $('#bulk_guard_name').val();

                if (!prefix) {
                    Swal.fire('Peringatan!', 'Masukkan prefix/module', 'warning');
                    return;
                }

                if (bulkActions.length === 0) {
                    Swal.fire('Peringatan!', 'Tambahkan minimal satu action', 'warning');
                    return;
                }

                $('#btn-bulk-save').prop('disabled', true).html(
                    '<i class="simple-icon-refresh"></i> Membuat...');

                $.ajax({
                    url: "{{ route('admin.permission.bulk-create') }}",
                    type: 'POST',
                    data: {
                        prefix: prefix,
                        actions: bulkActions,
                        guard_name: guardName
                    },
                    success: function(response) {
                        $('#bulkCreateModal').modal('hide');
                        table.ajax.reload();

                        var message = response.message;
                        if (response.data.skipped.length > 0) {
                            message += '<br><small>Dilewati (sudah ada): ' + response.data
                                .skipped.join(', ') + '</small>';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message ||
                            'Gagal membuat permissions', 'error');
                    },
                    complete: function() {
                        $('#btn-bulk-save').prop('disabled', false).html('Buat Permissions');
                    }
                });
            });

            // Assign Role Button
            $(document).on('click', '.btn-assign-role', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/permission') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        $('#role_permission_id').val(response.data.id);
                        $('#role_permission_name').text(response.data.name);

                        // Load roles
                        $.ajax({
                            url: "{{ route('admin.permission.roles', ':id') }}".replace(
                                ':id', id),
                            type: 'GET',
                            success: function(roleResponse) {
                                var rolesList = $('#rolesList');
                                rolesList.empty();

                                roleResponse.data.all_roles.forEach(function(role) {
                                    var isChecked = roleResponse.data
                                        .assigned_roles.includes(role.id) ?
                                        'checked' :
                                        '';

                                    var checkboxHtml = `
                                        <div class="checkbox-item">
                                            <input type="checkbox" class="mr-2" name="roles[]" value="${role.id}" ${isChecked}>
                                            <label>${role.name}</label>
                                        </div>
                                    `;
                                    rolesList.append(checkboxHtml);
                                });


                                $('#roleModal').modal('show');
                            },
                            error: function() {
                                Swal.fire('Error!', 'Gagal memuat daftar role',
                                    'error');
                            }
                        });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal memuat data permission', 'error');
                    }
                });
            });

            // Submit Role Form
            $('#roleForm').submit(function(e) {
                e.preventDefault();

                var permissionId = $('#role_permission_id').val();
                var selectedRoles = [];

                $('input[name="roles[]"]:checked').each(function() {
                    selectedRoles.push($(this).val());
                });

                $.ajax({
                    url: "{{ url('admin/permission') }}/" + permissionId + "/assign-role",
                    type: 'POST',
                    data: {
                        roles: selectedRoles
                    },
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message ||
                                'Gagal assign permission ke role'
                        });
                    }
                });
            });
        });
    </script>
@endpush
