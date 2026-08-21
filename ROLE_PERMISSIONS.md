# Role-Based & Permission-Based Access Control System

## Overview
This document explains the comprehensive access control system implemented for the Super Ittefaq Logistics Transport Management System. The system supports both role-based access control (RBAC) and fine-grained permission-based access control.

## Access Control Levels

### 1. Role-Based Access Control (RBAC)
The system uses roles to provide broad access control based on job functions:
- **Admin**: Full system access
- **Manager**: Operations + Financial access (except Users & Permissions, Settings)
- **Dispatcher**: Operations access only
- **Accounts**: Financial access only

### 2. Permission-Based Access Control
Admins can assign individual permissions to users for fine-grained control:
- Over 60 specific permissions across 5 modules
- Granular control (View, Create, Edit, Delete operations)
- Flexible permission assignment via user management interface

## User Roles

### 1. Admin
- **Full Access**: Complete system access including user management and settings
- **Operations**: Jobs, Dispatch, Trips, Tracking, POD, Vehicles, Drivers, Maintenance, Customers, Documents
- **Financial**: Invoices, Expenses, Reports
- **System**: Users & Permissions, Settings
- **Special**: Can assign individual permissions to any user

### 2. Manager
- **Operations Access**: Jobs, Dispatch, Trips, Tracking, POD, Vehicles, Drivers, Maintenance, Customers, Documents
- **Financial Access**: Invoices, Expenses, Reports
- **No Access**: Users & Permissions, Settings

### 3. Dispatcher
- **Operations Access**: Jobs, Dispatch, Trips, Tracking, POD, Vehicles, Drivers, Maintenance, Customers, Documents
- **No Access**: Financial modules (Invoices, Expenses, Reports), Users & Permissions, Settings

### 4. Accounts
- **Financial Access**: Invoices, Expenses, Reports
- **No Access**: Operations modules, Users & Permissions, Settings

## Permission Structure

### Modules & Permissions

#### Dashboard
- `view_dashboard`: Access to main dashboard

#### Operations Module
- `view_jobs`, `create_jobs`, `edit_jobs`, `delete_jobs`
- `view_dispatch`, `create_dispatch`, `edit_dispatch`, `delete_dispatch`
- `view_trips`, `create_trips`, `edit_trips`, `delete_trips`
- `view_tracking`: View live tracking
- `view_pod`, `create_pod`, `edit_pod`, `delete_pod`

#### Fleet & People Module
- `view_vehicles`, `create_vehicles`, `edit_vehicles`, `delete_vehicles`
- `view_drivers`, `create_drivers`, `edit_drivers`, `delete_drivers`
- `view_maintenance`, `create_maintenance`, `edit_maintenance`, `delete_maintenance`

#### Accounts Module
- `view_customers`, `create_customers`, `edit_customers`, `delete_customers`
- `view_invoices`, `create_invoices`, `edit_invoices`, `delete_invoices`
- `view_expenses`, `create_expenses`, `edit_expenses`, `delete_expenses`

#### System Module
- `view_documents`, `create_documents`, `edit_documents`, `delete_documents`, `download_documents`
- `view_reports`: View reports
- `view_users`, `create_users`, `edit_users`, `delete_users`, `manage_permissions`
- `view_settings`, `edit_settings`

## Implementation Details

### Database Structure
- **permissions table**: Stores all system permissions with name, slug, module, and description
- **permission_user table**: Pivot table linking users to their assigned permissions
- **users table**: Contains role field for role-based access

### User Model Enhancements
The User model (`app/Models/User.php`) includes:
- `permissions()`: Many-to-many relationship with Permission model
- `hasRole($role)`: Check if user has specific role
- `hasAnyRole(array $roles)`: Check if user has any of the specified roles
- `isAdmin()`, `isDispatcher()`, `isAccounts()`, `isManager()`: Role-specific helpers
- `hasPermission($permission)`: Check if user has specific permission
- `hasAnyPermission(array $permissions)`: Check if user has any of the specified permissions
- `givePermission($permission)`: Assign a permission to user
- `revokePermission($permission)`: Remove a permission from user
- `syncPermissions(array $permissions)`: Sync user permissions with provided array

### Middleware System

#### RoleMiddleware
- Checks if user is authenticated
- Validates user role against required roles
- Returns 403 Forbidden if unauthorized
- Supports multiple roles per route

#### PermissionMiddleware
- Checks if user is authenticated
- Validates user permissions against required permissions
- Returns 403 Forbidden if unauthorized
- Supports multiple permissions per route

### Route Protection
Routes are protected using role-based middleware:
- **Admin-only routes**: Users, Settings
- **Operations routes**: Admin, Dispatcher, Manager
- **Financial routes**: Admin, Accounts, Manager
- All routes require authentication (`auth` middleware)

### Permission Assignment Interface
Admin users can manage individual user permissions through:
1. Navigate to Users & Permissions page
2. Click the key icon (Manage Permissions) on any user
3. Select/deselect permissions by module
4. Save to apply changes

### Sidebar Navigation
The sidebar (`resources/views/partials/sidebar.blade.php`) dynamically shows menu items based on user roles:
- Operations section: Visible to Admin, Dispatcher, Manager
- Accounts section: Visible to all, with Customers for operations roles
- System section: Documents for operations, Reports for financial, Users/Settings for admin only

## Role Permissions Matrix

| Module | Admin | Manager | Dispatcher | Accounts |
|--------|-------|---------|------------|----------|
| Dashboard | ✅ | ✅ | ✅ | ✅ |
| Jobs/Bookings | ✅ | ✅ | ✅ | ❌ |
| Planning/Dispatch | ✅ | ✅ | ✅ | ❌ |
| Trips | ✅ | ✅ | ✅ | ❌ |
| Live Tracking | ✅ | ✅ | ✅ | ❌ |
| Proof of Delivery | ✅ | ✅ | ✅ | ❌ |
| Vehicles/Fleet | ✅ | ✅ | ✅ | ❌ |
| Drivers | ✅ | ✅ | ✅ | ❌ |
| Maintenance | ✅ | ✅ | ✅ | ❌ |
| Customers | ✅ | ✅ | ✅ | ❌ |
| Invoices | ✅ | ✅ | ❌ | ✅ |
| Expenses | ✅ | ✅ | ❌ | ✅ |
| Documents | ✅ | ✅ | ✅ | ❌ |
| Reports | ✅ | ✅ | ❌ | ✅ |
| Users & Permissions | ✅ | ❌ | ❌ | ❌ |
| Settings | ✅ | ❌ | ❌ | ❌ |

## Seeded Users
The system includes pre-configured users in `database/seeders/UserSeeder.php`:

1. **Admin User** (admin@ittefaq.com) - Admin role
2. **Sana Akhtar** (sana@superittefaq.co.uk) - Admin role  
3. **Usman Bhatti** (usman@superittefaq.co.uk) - Dispatcher role
4. **Nadia Zafar** (nadia@superittefaq.co.uk) - Accounts role
5. **Rizwan Khalid** (rizwan@superittefaq.co.uk) - Manager role (suspended)

All users have the default password: `password`

## Usage

### Role-Based Access
1. Login with any seeded user
2. Navigate to different modules based on role permissions
3. Unauthorized access attempts will show 403 Forbidden error
4. Sidebar menu items will be filtered based on user role

### Permission Assignment (Admin Only)
1. Login as admin user
2. Navigate to Users & Permissions page
3. Click the key icon (Manage Permissions) on any user
4. Select/deselect individual permissions by module
5. Click "Save Permissions" to apply changes
6. User will now have access to modules based on assigned permissions

## Security Features
- All protected routes require authentication
- Role middleware checks user permissions before allowing access
- Permission middleware provides fine-grained access control
- Unauthorized access returns 403 Forbidden with descriptive message
- Sidebar menu items are conditionally rendered based on permissions
- User status (active/suspended) can be managed by admin
- Permission assignments are stored in database for persistence
- Permission system works alongside role-based access for maximum flexibility

## Future Enhancements
- Implement permission-based route protection for individual actions
- Add permission caching for improved performance
- Create permission templates for quick user setup
- Add permission audit logging
- Implement permission inheritance for related operations