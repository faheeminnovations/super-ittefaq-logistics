@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">System</div>
        <h1>Users & Permissions</h1>
        <div class="sub">Admin, Dispatcher, Accounts, Driver & Manager roles</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportUsers()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Add User</button>
      </div>
    </div>
    
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-shield-lock"></i></div>
          <div class="label">Total Users</div>
          <div class="value">{{ $totalUsers ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> All roles</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-person-check"></i></div>
          <div class="label">Active</div>
          <div class="value">{{ $activeUsers ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Active accounts</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-person-x"></i></div>
          <div class="label">Suspended</div>
          <div class="value">{{ $suspendedUsers ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Inactive accounts</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#F3E3B8;color:#8A6512;"><i class="bi bi-key"></i></div>
          <div class="label">Admins</div>
          <div class="value">{{ $adminUsers ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Full access</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('active')">Active</span> 
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('suspended')">Suspended</span> 
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">User Directory</div>
          <div class="panel-sub mb-0">All system accounts and role assignments</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="usersTable">
          <thead>
            <tr>
              <th>User</th>
              <th>Role</th>
              <th>Email</th>
              <th>Last Active</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($users)
              @foreach($users as $user)
            <tr>
              <td>
                <div class='driver-mini'>
                  <div class='av'>{{ substr($user->name, 0, 2) }}</div>
                  {{ $user->name }}
                </div>
              </td>
              <td>{{ ucfirst($user->role) }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->updated_at ? \Carbon\Carbon::parse($user->updated_at)->diffForHumans() : 'N/A' }}</td>
              <td>
                @switch($user->status)
                  @case('active')
                    <span class="badge-status badge-delivered">Active</span>
                    @break
                  @case('suspended')
                    <span class="badge-status badge-delayed">Suspended</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst($user->status) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editUser({{ $user->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewUser({{ $user->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-success" onclick="managePermissions({{ $user->id }})" title="Manage Permissions">
                    <i class="bi bi-key"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteUser({{ $user->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="6" class="text-center">No users found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit User Modal -->
  <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalLabel">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="userForm" action="{{ route('users.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="user_id" id="user_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" @if(!isset($user)) required @endif>
                <small class="text-muted" id="passwordHelp">Leave blank to keep current password</small>
              </div>
              <div class="col-md-6 mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" name="role" id="role" required>
                  <option value="admin">Admin</option>
                  <option value="dispatcher">Dispatcher</option>
                  <option value="accounts">Accounts</option>
                  <option value="driver">Driver</option>
                  <option value="manager">Manager</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="active">Active</option>
                  <option value="suspended">Suspended</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save User</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View User Modal -->
  <div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">User Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewUserContent">
          <!-- Content will be loaded dynamically -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Permissions Modal -->
  <div class="modal fade" id="permissionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Manage User Permissions</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="permissionsForm" action="#" method="POST">
          @csrf
          <input type="hidden" name="user_id" id="permission_user_id">
          <div class="modal-body" id="permissionsContent">
            <!-- Permissions will be loaded dynamically -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Permissions</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#usersTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: 5 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search users..."
        }
    });

    // Form submission
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var userId = $('#user_id').val();
        var url = userId ? '/users/' + userId : '{{ route("users.store") }}';
        var method = 'POST';
        
        // Set _method to PUT for updates
        if (userId) {
            $('#_method').val('PUT');
        } else {
            $('#_method').val('POST');
        }
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#userModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving user';
                if (errors) {
                    errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value + '\n';
                    });
                }
                alert(errorMessage);
            }
        });
    });
});

function resetForm() {
    $('#userForm')[0].reset();
    $('#user_id').val('');
    $('#_method').val('POST');
    $('#password').prop('required', true);
    $('#passwordHelp').text('');
    $('#userModalLabel').text('Add User');
}

function editUser(id) {
    $.ajax({
        url: '/users/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(user) {
            fillUserForm(user);
            $('#userModal').modal('show');
        },
        error: function() {
            alert('Error loading user data');
        }
    });
}

function fillUserForm(user) {
    $('#user_id').val(user.id);
    $('#name').val(user.name);
    $('#email').val(user.email);
    $('#password').val('');
    $('#password').prop('required', false);
    $('#passwordHelp').text('Leave blank to keep current password');
    $('#role').val(user.role);
    $('#status').val(user.status);
    
    $('#userModalLabel').text('Edit User');
}

function viewUser(id) {
    $.ajax({
        url: '/users/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var user = data.user || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>User Information</h6>
                        <p><strong>Name:</strong> ${user.name}</p>
                        <p><strong>Email:</strong> ${user.email}</p>
                        <p><strong>Role:</strong> ${user.role}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Account Status</h6>
                        <p><strong>Status:</strong> ${user.status}</p>
                        <p><strong>Last Active:</strong> ${user.updated_at ? new Date(user.updated_at).toLocaleString() : 'N/A'}</p>
                    </div>
                </div>
            `;
            $('#viewUserContent').html(html);
            $('#viewUserModal').modal('show');
        },
        error: function() {
            alert('Error loading user details');
        }
    });
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        $.ajax({
            url: '/users/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error deleting user');
            }
        });
    }
}

function filterByStatus(status) {
    var table = $('#usersTable').DataTable();
    if (status === 'all') {
        table.column(4).search('').draw();
    } else {
        table.column(4).search(status).draw();
    }
}

function exportUsers() {
    alert('Export functionality will be implemented to export users data as CSV/Excel');
}

function managePermissions(userId) {
    $('#permission_user_id').val(userId);
    
    $.ajax({
        url: '/users/' + userId + '/permissions/data',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            renderPermissions(data);
            $('#permissionsModal').modal('show');
        },
        error: function() {
            alert('Error loading permissions');
        }
    });
}

function renderPermissions(data) {
    var userPermissions = data.user_permissions;
    var allPermissions = data.all_permissions;
    var html = '';
    
    var moduleNames = {
        'dashboard': 'Dashboard',
        'operations': 'Operations',
        'fleet': 'Fleet & People',
        'accounts': 'Accounts',
        'system': 'System'
    };
    
    $.each(allPermissions, function(module, permissions) {
        html += '<div class="card mb-3">';
        html += '<div class="card-header bg-light"><h6 class="mb-0">' + (moduleNames[module] || module) + '</h6></div>';
        html += '<div class="card-body">';
        html += '<div class="row">';
        
        $.each(permissions, function(index, permission) {
            var isChecked = userPermissions.includes(permission.slug) ? 'checked' : '';
            html += '<div class="col-md-4 mb-2">';
            html += '<div class="form-check">';
            html += '<input class="form-check-input" type="checkbox" name="permissions[]" value="' + permission.slug + '" id="perm_' + permission.slug + '" ' + isChecked + '>';
            html += '<label class="form-check-label" for="perm_' + permission.slug + '">';
            html += permission.name;
            html += '</label>';
            html += '</div>';
            html += '</div>';
        });
        
        html += '</div>';
        html += '</div>';
        html += '</div>';
    });
    
    $('#permissionsContent').html(html);
}

// Handle permissions form submission
$('#permissionsForm').on('submit', function(e) {
    e.preventDefault();
    
    var userId = $('#permission_user_id').val();
    var formData = $(this).serialize();
    
    $.ajax({
        url: '/users/' + userId + '/permissions',
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#permissionsModal').modal('hide');
            alert('Permissions updated successfully');
        },
        error: function(xhr) {
            var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
            var errorMessage = 'Error updating permissions';
            if (errors) {
                errorMessage = '';
                $.each(errors, function(key, value) {
                    errorMessage += value + '\n';
                });
            }
            alert(errorMessage);
        }
    });
});
</script>
@endpush

