@extends('layouts.app')

@section('title', 'Manajemen User')

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

        .permission-section {
            margin-bottom: 20px;
        }

        .permission-section h6 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #495057;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Manajemen Menu</h1>
                <div class="top-right-button-container">
                    <button class="btn btn-primary btn-sm" id="btn-add">
                        <i class="simple-icon-plus"></i> Tambah User
                    </button>
                </div>
                {{-- <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                    <ol class="breadcrumb pt-0">
                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#">Library</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Data</li>
                    </ol>
                </nav> --}}
                <div class="separator mb-5"></div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="userTable">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th>Permissions</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th width="20%">Aksi</th>
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

    <!-- Modal Form User -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah User</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="userForm">
                    <div class="modal-body">
                        <input type="hidden" id="user_id" name="user_id">

                        <div class="form-group">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password <span class="text-danger password-required">*</span></label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted password-hint">
                                Minimal 8 karakter
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password <span
                                    class="text-danger password-required">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                        </div>

                        <div class="form-group row mb-1">
                            <label class="col-12 col-form-label">Status Aktif</label>
                            <div class="col-12">
                                <div class="custom-switch custom-switch-small custom-switch-secondary mb-2">
                                    <input class="custom-switch-input" id="is_active" name="is_active" type="checkbox"
                                        checked>
                                    <label class="custom-switch-btn" for="is_active"></label>
                                </div>
                            </div>
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

    <!-- Modal Assign Role -->
    <div class="modal fade" id="roleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Role ke User</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="roleForm">
                    <div class="modal-body">
                        <input type="hidden" id="role_user_id" name="user_id">

                        <div class="alert alert-info">
                            <strong>User:</strong> <span id="role_user_name"></span>
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

    <!-- Modal Assign Permission -->
    <div class="modal fade" id="permissionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Permission ke User</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="permissionForm">
                    <div class="modal-body">
                        <input type="hidden" id="permission_user_id" name="user_id">

                        <div class="alert alert-info">
                            <strong>User:</strong> <span id="permission_user_name"></span>
                        </div>

                        <div class="alert alert-warning">
                            <strong>Catatan:</strong> Ini adalah <strong>Direct Permissions</strong> untuk user.
                            User juga mendapatkan permissions dari role yang di-assign.
                        </div>

                        <div class="permission-section">
                            <h6>Direct Permissions</h6>
                            <small class="text-muted">Permissions yang langsung di-assign ke user (tidak dari role)</small>
                            <div class="checkbox-list" id="permissionsList">
                                <!-- Will be populated via AJAX -->
                            </div>
                        </div>

                        <div class="permission-section">
                            <h6>Permissions dari Role <span class="badge badge-info" id="rolePermissionsCount">0</span>
                            </h6>
                            <div id="rolePermissionsList" class="text-muted">
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

    <!-- Modal Reset Password -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="resetPasswordForm">
                    <div class="modal-body">
                        <input type="hidden" id="reset_user_id" name="user_id">

                        <div class="alert alert-info">
                            <strong>User:</strong> <span id="reset_user_name"></span>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <small class="form-text text-muted">Minimal 8 karakter</small>
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation">Konfirmasi Password Baru <span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="new_password_confirmation"
                                name="new_password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Reset Password</button>
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
            var table = $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.user.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role_names',
                        name: 'role_names',
                        orderable: false
                    },
                    {
                        data: 'permissions_count',
                        name: 'permissions_count',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'is_active'
                    },
                    {
                        data: 'last_login_formatted',
                        name: 'last_login'
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
                language: {
                    processing: "Memuat data...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    emptyTable: "Tidak ada data tersedia",
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir"
                    }
                }
            });

            // Add Button
            $('#btn-add').click(function() {
                $('#userForm')[0].reset();
                $('#user_id').val('');
                $('#modalTitle').text('Tambah User');
                $('#password').prop('required', true);
                $('#password_confirmation').prop('required', true);
                $('.password-required').show();
                $('.password-hint').text('Minimal 8 karakter');
                $('#is_active').prop('checked', true);
                $('#userModal').modal('show');
            });

            // Edit Button
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/user') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        $('#user_id').val(response.data.id);
                        $('#username').val(response.data.username);
                        $('#email').val(response.data.email);
                        $('#is_active').prop('checked', response.data.is_active);

                        $('#password').prop('required', false);
                        $('#password_confirmation').prop('required', false);
                        $('.password-required').hide();
                        $('.password-hint').text(
                            'Kosongkan jika tidak ingin mengubah password');

                        $('#modalTitle').text('Edit User');
                        $('#userModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal memuat data user', 'error');
                    }
                });
            });

            // Submit Form
            $('#userForm').submit(function(e) {
                e.preventDefault();

                var id = $('#user_id').val();
                var url = id ? "{{ url('admin/user') }}/" + id : "{{ route('admin.user.store') }}";
                var type = id ? 'PUT' : 'POST';

                var formData = {
                    username: $('#username').val(),
                    email: $('#email').val(),
                    password: $('#password').val(),
                    password_confirmation: $('#password_confirmation').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0
                };

                $('#btn-save').prop('disabled', true).html(
                    '<i class="simple-icon-refresh"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    success: function(response) {
                        $('#userModal').modal('hide');
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

            // Toggle Status Button
            $(document).on('click', '.btn-toggle-status', function() {
                var id = $(this).data('id');
                var status = $(this).data('status');
                var statusText = status == 1 ? 'mengaktifkan' : 'menonaktifkan';

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin ' + statusText + ' user ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/user') }}/" + id + "/toggle-status",
                            type: 'POST',
                            data: {
                                status: status
                            },
                            success: function(response) {
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
                                        'Gagal mengubah status user'
                                });
                            }
                        });
                    }
                });
            });

            // Delete Button
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data user akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/user') }}/" + id,
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
                                        'Gagal menghapus user'
                                });
                            }
                        });
                    }
                });
            });

            // Assign Role Button
            $(document).on('click', '.btn-assign-role', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/user') }}/" + id + "/roles",
                    type: 'GET',
                    success: function(response) {
                        $('#role_user_id').val(response.data.user.id);
                        $('#role_user_name').text(response.data.user.username + ' (' + response
                            .data.user.email + ')');

                        var rolesList = $('#rolesList');
                        rolesList.empty();

                        response.data.all_roles.forEach(function(role) {
                            var isChecked = response.data.assigned_roles.includes(role
                                .id) ? 'checked' : '';
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
                        Swal.fire('Error!', 'Gagal memuat data role', 'error');
                    }
                });
            });

            // Submit Role Form
            $('#roleForm').submit(function(e) {
                e.preventDefault();

                var userId = $('#role_user_id').val();
                var selectedRoles = [];

                $('input[name="roles[]"]:checked').each(function() {
                    selectedRoles.push($(this).val());
                });

                $.ajax({
                    url: "{{ url('admin/user') }}/" + userId + "/assign-role",
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
                                'Gagal assign role ke user'
                        });
                    }
                });
            });

            // Assign Permission Button
            $(document).on('click', '.btn-assign-permission', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/user') }}/" + id + "/permissions",
                    type: 'GET',
                    success: function(response) {
                        $('#permission_user_id').val(response.data.user.id);
                        $('#permission_user_name').text(response.data.user.username + ' (' +
                            response.data.user.email + ')');

                        var permissionsList = $('#permissionsList');
                        permissionsList.empty();

                        response.data.all_permissions.forEach(function(permission) {
                            var isChecked = response.data.direct_permissions.includes(
                                permission.id) ? 'checked' : '';
                            var isFromRole = response.data.role_permissions.includes(
                                permission.id);
                            var badge = isFromRole ?
                                ' <span class="badge badge-info badge-sm">dari role</span>' :
                                '';

                            var checkboxHtml = `
                                <div class="checkbox-item">
                                    <input type="checkbox" class="mr-2" name="permissions[]" value="${permission.id}" ${isChecked}>
                                    <label>${permission.name}${badge}</label>
                                </div>
                            `;
                            permissionsList.append(checkboxHtml);
                        });

                        // Show role permissions
                        var rolePermissionsList = $('#rolePermissionsList');
                        var rolePermCount = response.data.role_permissions.length;
                        $('#rolePermissionsCount').text(rolePermCount);

                        if (rolePermCount > 0) {
                            var rolePerms = response.data.all_permissions
                                .filter(p => response.data.role_permissions.includes(p.id))
                                .map(p => p.name)
                                .join(', ');
                            rolePermissionsList.html('<small>' + rolePerms + '</small>');
                        } else {
                            rolePermissionsList.html(
                                '<small class="text-muted">User belum memiliki permission dari role</small>'
                            );
                        }

                        $('#permissionModal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal memuat data permission', 'error');
                    }
                });
            });

            // Submit Permission Form
            $('#permissionForm').submit(function(e) {
                e.preventDefault();

                var userId = $('#permission_user_id').val();
                var selectedPermissions = [];

                $('input[name="permissions[]"]:checked').each(function() {
                    selectedPermissions.push($(this).val());
                });

                $.ajax({
                    url: "{{ url('admin/user') }}/" + userId + "/assign-permission",
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message ||
                                'Gagal assign permission ke user'
                        });
                    }
                });
            });
        });
    </script>
@endpush
