@extends('layouts.app')

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
            padding: 10px;
            border-radius: 4px;
        }

        .checkbox-item {
            padding: 5px 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Blank Page</h1>
                <div class="top-right-button-container">
                    <button class="btn btn-primary btn-sm" id="btn-add">
                        <i class="simple-icon-plus"></i> Tambah Menu
                    </button>
                </div>
                <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                    <ol class="breadcrumb pt-0">
                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#">Library</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Data</li>
                    </ol>
                </nav>
                <div class="separator mb-5"></div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="menuTable">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Menu</th>
                                        <th>Slug</th>
                                        <th>Parent</th>
                                        <th>URL</th>
                                        <th>Icon</th>
                                        <th>Order</th>
                                        <th>Roles</th>
                                        <th>Permissions</th>
                                        <th>Status</th>
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
    <div class="modal fade" id="menuModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Menu</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="menuForm">
                    <div class="modal-body">
                        <input type="hidden" id="menu_id" name="menu_id">

                        <div class="form-group">
                            <label for="name">Nama Menu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <small class="form-text text-muted">Nama menu yang akan ditampilkan</small>
                        </div>

                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug">
                            <small class="form-text text-muted">Kosongkan untuk auto-generate dari nama</small>
                        </div>

                        <div class="form-group">
                            <label for="icon">Icon</label>
                            <input type="text" class="form-control" id="icon" name="icon"
                                placeholder="simple-icon-home">
                            <small class="form-text text-muted">Contoh: simple-icon-home, iconsminds-shop-4</small>
                        </div>

                        <div class="form-group">
                            <label for="url">URL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="url" name="url" required
                                placeholder="/dashboard">
                            <small class="form-text text-muted">URL route menu (gunakan # untuk parent menu)</small>
                        </div>

                        <div class="form-group">
                            <label for="parent_id">Parent Menu</label>
                            <select class="form-control" id="parent_id" name="parent_id">
                                <option value="">-- Tidak Ada Parent (Menu Utama) --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="order">Urutan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="order" name="order" value="0" required
                                min="0">
                            <small class="form-text text-muted">Urutan tampilan menu (0 = paling atas)</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
                                <label class="custom-control-label" for="is_active">Status Aktif</label>
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
                    <h5 class="modal-title">Assign Role ke Menu</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="roleForm">
                    <div class="modal-body">
                        <input type="hidden" id="role_menu_id" name="menu_id">

                        <div class="alert alert-info">
                            <strong>Menu:</strong> <span id="role_menu_name"></span>
                        </div>

                        <div class="form-group">
                            <label>Pilih Role <span class="text-danger">*</span></label>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Permission ke Menu</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="permissionForm">
                    <div class="modal-body">
                        <input type="hidden" id="permission_menu_id" name="menu_id">

                        <div class="alert alert-info">
                            <strong>Menu:</strong> <span id="permission_menu_name"></span>
                        </div>

                        <div class="form-group">
                            <label>Pilih Permission <span class="text-danger">*</span></label>
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
            var table = $('#menuTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.menu.index') }}",
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
                        data: 'slug',
                        name: 'slug'
                    },
                    {
                        data: 'parent_name',
                        name: 'parent.name'
                    },
                    {
                        data: 'url',
                        name: 'url'
                    },
                    {
                        data: 'icon',
                        name: 'icon'
                    },
                    {
                        data: 'order',
                        name: 'order'
                    },
                    {
                        data: 'roles_list',
                        name: 'roles_list',
                        orderable: false
                    },
                    {
                        data: 'permissions_list',
                        name: 'permissions_list',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'is_active'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [6, 'asc']
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

            // Load Parent Menus
            function loadParentMenus() {
                $.ajax({
                    url: "{{ route('admin.menu.parents') }}",
                    type: 'GET',
                    success: function(response) {
                        var options = '<option value="">-- Tidak Ada Parent (Menu Utama) --</option>';
                        $.each(response.data, function(index, menu) {
                            options += '<option value="' + menu.id + '">' + menu.name +
                                '</option>';
                        });
                        $('#parent_id').html(options);
                    }
                });
            }

            // Auto generate slug from name
            $('#name').on('keyup', function() {
                var name = $(this).val();
                var slug = name.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g, '');
                $('#slug').val(slug);
            });

            // Add Button
            $('#btn-add').click(function() {
                $('#menuForm')[0].reset();
                $('#menu_id').val('');
                $('#modalTitle').text('Tambah Menu');
                $('#is_active').prop('checked', true);
                loadParentMenus();
                $('#menuModal').modal('show');
            });

            // Edit Button
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/menu') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        loadParentMenus();

                        setTimeout(function() {
                            $('#menu_id').val(response.data.id);
                            $('#name').val(response.data.name);
                            $('#slug').val(response.data.slug);
                            $('#icon').val(response.data.icon);
                            $('#url').val(response.data.url);
                            $('#parent_id').val(response.data.parent_id || '');
                            $('#order').val(response.data.order);
                            $('#is_active').prop('checked', response.data.is_active);

                            $('#modalTitle').text('Edit Menu');
                            $('#menuModal').modal('show');
                        }, 300);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal memuat data menu', 'error');
                    }
                });
            });

            // Submit Form
            $('#menuForm').submit(function(e) {
                e.preventDefault();

                var id = $('#menu_id').val();
                var url = id ? "{{ url('admin/menu') }}/" + id : "{{ route('admin.menu.store') }}";
                var type = id ? 'PUT' : 'POST';

                var formData = {
                    name: $('#name').val(),
                    slug: $('#slug').val(),
                    icon: $('#icon').val(),
                    url: $('#url').val(),
                    parent_id: $('#parent_id').val() || null,
                    order: $('#order').val(),
                    is_active: $('#is_active').is(':checked') ? 1 : 0
                };

                $('#btn-save').prop('disabled', true).html(
                    '<i class="simple-icon-refresh"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    success: function(response) {
                        $('#menuModal').modal('hide');
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
                    text: "Data menu akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/menu') }}/" + id,
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
                                        'Gagal menghapus menu'
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
                    url: "{{ url('admin/menu') }}/" + id + "/roles",
                    type: 'GET',
                    success: function(response) {
                        $('#role_menu_id').val(id);
                        $('#role_menu_name').text(response.data.menu.name);

                        var rolesList = '';
                        $.each(response.data.all_roles, function(index, role) {
                            var checked = response.data.assigned_roles.includes(role
                                .id) ? 'checked' : '';
                            rolesList += `
                        <div class="checkbox-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="role_${role.id}"
                                       name="roles[]" value="${role.id}" ${checked}>
                                <label class="custom-control-label" for="role_${role.id}">
                                    ${role.name}
                                </label>
                            </div>
                        </div>
                    `;
                        });

                        $('#rolesList').html(rolesList);
                        $('#roleModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal memuat data role', 'error');
                    }
                });
            });

            // Submit Role Form
            $('#roleForm').submit(function(e) {
                e.preventDefault();

                var id = $('#role_menu_id').val();
                var selectedRoles = [];

                $('input[name="roles[]"]:checked').each(function() {
                    selectedRoles.push($(this).val());
                });

                if (selectedRoles.length === 0) {
                    Swal.fire('Peringatan!', 'Pilih minimal satu role', 'warning');
                    return;
                }

                $.ajax({
                    url: "{{ url('admin/menu') }}/" + id + "/roles",
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
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Gagal assign role',
                            'error');
                    }
                });
            });

            // Assign Permission Button
            $(document).on('click', '.btn-assign-permission', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/menu') }}/" + id + "/permissions",
                    type: 'GET',
                    success: function(response) {
                        $('#permission_menu_id').val(id);
                        $('#permission_menu_name').text(response.data.menu.name);

                        var permissionsList = '';
                        $.each(response.data.all_permissions, function(index, permission) {
                            var checked = response.data.assigned_permissions.includes(
                                permission.id) ? 'checked' : '';
                            permissionsList += `
                        <div class="checkbox-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="permission_${permission.id}"
                                    name="permissions[]" value="${permission.id}" ${checked}>
                                <label class="custom-control-label" for="permission_${permission.id}">
                                    ${permission.name}
                                </label>
                            </div>
                        </div>
                    `;
                        });

                        $('#permissionsList').html(permissionsList);
                        $('#permissionModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal memuat data permission', 'error');
                    }
                });
            });

            // Submit Permission Form
            $('#permissionForm').submit(function(e) {
                e.preventDefault();

                var id = $('#permission_menu_id').val();
                var selectedPermissions = [];

                $('input[name="permissions[]"]:checked').each(function() {
                    selectedPermissions.push($(this).val());
                });

                if (selectedPermissions.length === 0) {
                    Swal.fire('Peringatan!', 'Pilih minimal satu permission', 'warning');
                    return;
                }

                $.ajax({
                    url: "{{ url('admin/menu') }}/" + id + "/permissions",
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
