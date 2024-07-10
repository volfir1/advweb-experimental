@extends('layouts.app')

@section('content')
<div id="order-content">
    <h1 class="centered-header">User Management Dashboard</h1>
    <div id="message"></div>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col col-sm-9">Manage Users</div>
                    <div class="col col-sm-3">
                        <button type="button" id="import_excel" class="btn btn-info btn-sm float-end me-2">Import to Excel</button>
                        <button type="button" id="export_excel" class="btn btn-info btn-sm float-end me-2">Export to Excel</button>
                        <button type="button" id="add_data" class="btn btn-success btn-sm float-end">Add</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="sample_data">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profile Image</th>
                                <th>Username</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Active Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Add/Edit -->
<div class="modal" tabindex="-1" id="action_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="sample_form">
                <div class="modal-header">
                    <h5 class="modal-title" id="dynamic_modal_title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="name" id="name" class="form-control" required />
                        <span id="name_error" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" required />
                        <span id="first_name_error" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" required />
                        <span id="last_name_error" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required />
                        <span id="email_error" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" />
                        <span id="password_error" class="text-danger"></span>
                    </div>
                    <!-- Add this inside your modal form, after the password field -->
                    <div class="mb-3" id="password_confirm_group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control" />
                        <span id="password_confirm_error" class="text-danger"></span>
                        </div>
                        <select class="form-select" id="active_status" name="active_status" >
                                <option value="1">Active</option>
                                 <option value="0">Inactive</option>
                                </select>
                        <span id="active_status_error" class="text-danger"></span>

                   
                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" id="profile_image" class="form-control" />
                        <span id="profile_image_error" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" name="contact" id="contact" class="form-control" required />
                        <span id="contact_error" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" id="address" class="form-control" required />
                        <span id="address_error" class="text-danger"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="id" />
                    <input type="hidden" name="action" id="action" value="Add" />
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="action_button">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // DataTable initialization for displaying users
    var dataTable = $('#sample_data').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('api.admin.fetchUsers') }}",
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.search = $('input[type="search"]').val();
            },
            dataSrc: function(json) {
                if (json.data.length === 0) {
                    $('#sample_data tbody').html('<tr><td colspan="10" class="text-center">No records available</td></tr>');
                }
                return json.data;
            }
        },
        columns: [
            { data: 'id', name: 'id' }, // ID column
            { 
                data: 'profile_image', 
                name: 'profile_image',
                render: function(data, type, full, meta) {
                    if (type === 'display') {
                        return '<img src="' + (data ? data : 'default-placeholder.png') + '" alt="Profile Image" class="img-thumbnail rounded-circle" width="30" height="30">';
                    }
                    return data;
                }
            },
            { data: 'name', name: 'name', title: 'Username' }, // Username column
            { data: 'fname', name: 'fname', title: 'First Name' }, // First Name column
            { data: 'lname', name: 'lname', title: 'Last Name' }, // Last Name column
            { data: 'email', name: 'email' }, // Email column
            { data: 'contact', name: 'contact' }, // Contact column
            { data: 'address', name: 'address' }, // Address column
            { 
                data: 'active_status', 
                name: 'active_status',
                render: function(data, type, full, meta) {
                    return data ? 'Active' : 'Inactive';
                }
            }, // Active Status column
            {
                data: null,
                render: function(data, type, row) {
                    return '<button class="btn btn-primary btn-sm edit" data-id="' + row.id + '">Edit</button> ' +
                           '<button class="btn btn-danger btn-sm delete" data-id="' + row.id + '">Delete</button>';
                }
            } // Edit and Delete buttons column
        ],
        searching: true,
        language: {
            emptyTable: "No records available",
            zeroRecords: "No matching records found"
        },
        drawCallback: function(settings) {
            var api = this.api();
            var pageInfo = api.page.info();
            
            if (pageInfo.pages <= 1) {
                $('#sample_data_paginate').hide();
            } else {
                $('#sample_data_paginate').show();
            }

            if (api.rows({ filter: 'applied' }).count() === 0) {
                $('#sample_data tbody').html('<tr><td colspan="9" class="text-center">No matching records found</td></tr>');
            }
        }
    });

    // Custom search with delay
    var searchTimeout;
    $('#sample_data_filter input').unbind().bind('keyup', function() {
        clearTimeout(searchTimeout);
        var that = this;
        searchTimeout = setTimeout(function() {
            dataTable.search(that.value).draw();
        }, 300);
    });

    // Handle Add Button (Create operation)
    $('#add_data').click(function() {
        $('#sample_form')[0].reset();
    $('#sample_form').find('span.text-danger').html('');
    $('#dynamic_modal_title').text('Add User');
    $('#action_button').text('Add');
    $('#action').val('Add');
    $('#id').val('');
    
    // Set active status to always active (1) and disable the field
    $('#active_status').val('1').prop('disabled', true);
    
    $('#password_confirm_group').show(); // Show confirm password field
    $('#action_modal').modal('show');
    });

    // Handle Edit Button (Update operation)
    $(document).on('click', '.edit', function() {
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('admin.getEditUserData', ['id' => ':id']) }}".replace(':id', id),
            method: "GET",
            success: function(data) {
                $('#name').val(data.name);
                $('#first_name').val(data.first_name);
                $('#last_name').val(data.last_name);
                $('#email').val(data.email);
                $('#password').val(''); // Clear the password field
                $('#active_status').prop('disabled', false);
                $('#active_status').val('1');// Set dropdown value based on active_status
                $('#contact').val(data.contact);
                $('#address').val(data.address);
                $('#id').val(data.id);
                $('#dynamic_modal_title').text('Edit User');
                $('#action_button').text('Edit');
                $('#action').val('Edit');
                $('#action_modal').modal('show');
            },
            error: function(xhr, status, error) {
                console.log('Error fetching user data: ' + error);
            }
        });
    });

    // Handle Delete Button (Delete operation)
    $(document).on('click', '.delete', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: "{{ route('api.admin.deleteUser', ':id') }}".replace(':id', id),
                method: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    $('#message').html('<div class="alert alert-success">User deleted successfully</div>');
                    dataTable.ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting user:', error);
                }
            });
        }
    });

    // Handle Form Submit (Create and Update operations)
    $('#sample_form').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        var url = "{{ route('admin.updateUserData') }}";
        var isValid = true;
        
        // Validate required fields
        $('#sample_form input[required], #sample_form select[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).next('.text-danger').text('This field is required.');
            } else {
                $(this).next('.text-danger').text('');
            }
        });

        // Email validation
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test($('#email').val())) {
            isValid = false;
            $('#email_error').text('Please enter a valid email address.');
        }

        // Check password validation on Add action
        if ($('#action').val() === 'Add') {
            if ($('#password').val().length < 8) {
                isValid = false;
                $('#password_error').text('Password must be at least 8 characters long.');
            }
            if ($('#password').val() !== $('#password_confirm').val()) {
                isValid = false;
                $('#password_confirm_error').text('Passwords do not match.');
            }
        }

        if (!isValid) {
            return false;
        }

        // AJAX call to handle form submission
        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.success) {
                    $('#message').html('<div class="alert alert-success">' + data.success + '</div>');
                    $('#action_modal').modal('hide');
                    dataTable.ajax.reload();
                }
            },
            error: function(xhr, status, error) {
                var errors = xhr.responseJSON.errors;
                for (var key in errors) {
                    $('#' + key + '_error').text(errors[key][0]);
                }
            }
        });
    });

    // Handle Export to Excel Button (Export operation)
    $('#export_excel').click(function() {
        window.location.href = "{{ route('api.admin.exportUsers') }}";
    });

    // Handle Import from Excel Button (Import operation)
    $('#import_excel').click(function() {
        $('#import_modal').modal('show');
    });

    // Handle Import Form Submit (Import operation)
    $('#import_form').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);

        $.ajax({
            url: "{{ route('api.admin.importUsers') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                $('#import_message').html('<div class="alert alert-success">' + data.message + '</div>');
                $('#import_modal').modal('hide');
                dataTable.ajax.reload();
            },
            error: function(xhr, status, error) {
                var errors = xhr.responseJSON.errors;
                var errorHtml = '<ul>';
                $.each(errors, function(key, value) {
                    errorHtml += '<li>' + value + '</li>';
                });
                errorHtml += '</ul>';
                $('#import_message').html('<div class="alert alert-danger">' + errorHtml + '</div>');
            }
        });
    });

    // Handle Clear Button in Import Modal (Clear import form operation)
    $('#clear_import_data').click(function() {
        $('#import_form')[0].reset();
        $('#import_message').html('');
    });

});

</script>
@endpush
