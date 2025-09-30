@extends('layouts.app')

@section('title', 'Create Role')
@section('page_title', 'Role')
@section('page_subtitle', 'Create New Role')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Create New Role</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Management</li>
            <li><a href="{{ route('role.index') }}">Roles</a></li>
            <li class="opacity-80">Create</li>
        </ul>
    </div>
</div>

<form action="#" method="POST">
    @csrf

    <!-- Basic Information Section -->
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column - Role Details -->
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <div class="card-title"><span class="iconify lucide--info size-4"></span>Role Details</div>
                <fieldset class="fieldset mt-2 grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label class="fieldset-label" for="name">
                            Role Name <span class="text-error">*</span>
                        </label>
                        <input class="input w-full" id="name" placeholder="e.g., store_manager" type="text">
                        <p class="text-base-content/60">
                            * System identifier Lowercase letters and underscores only
                        </p>
                    </div>
                    <div class="space-y-2">
                        <label class="fieldset-label" for="name">
                            Display Name <span class="text-error">*</span>
                        </label>
                        <input class="input w-full" id="name" placeholder="Store Manager" type="text">
                    </div>
                    <div class="col-span-1 space-y-2 lg:col-span-2">
                        <label class="fieldset-label" for="description">
                            Description
                        </label>
                        <textarea placeholder="Description" id="description" class="textarea w-full"></textarea>
                    </div>
                </fieldset>
            </div>
        </div>

        <!-- Right Column - Role Settings -->
        <div class="bg-base-100 card shadow">
            <div class="card-body">
                <div class="card-title"> <span class="iconify lucide--settings size-4"></span>Role Setting</div>
                <fieldset class="fieldset mt-2 grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="space-y-2">
                        <label class="fieldset-label" for="name">
                            Priority Level
                            <span class="text-error">*</span>
                        </label>
                        <input min="0" max="100" class="input w-full" id="priority" placeholder="priority" type="number" name="priority" value="50" />
                        <p class="text-base-content/60">
                            * Higher = More Important Used for permission conflict resolution
                        </p>
                    </div>
                    <div class="space-y-2">
                        <label class="fieldset-label" for="name">
                            Status
                        </label>
                        <select class="select w-full" aria-label="Select Category" id="category" name="is_active">
                            <option value="1"> Active </option>
                            <option value="0"> Inactive </option>
                        </select>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="fieldset-label" for="description">
                            System Role
                        </label>
                        <input class="checkbox checkbox-sm" type="checkbox"  name="is_system">
                        <p class="text-base-content/60">
                            * System roles cannot be deleted
                        </p>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>

    <!-- Module Access & Permissions Section -->
    <div class="mt-6 bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h3 class="card-title text-base">
                    <span class="iconify lucide--shield size-4"></span>
                    Module Access & Permissions
                </h3>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-sm btn-ghost" onclick="selectAllPermissions()">
                        <span class="checkbox checkbox-sm size-4"></span>
                        Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-ghost" onclick="deselectAllPermissions()">
                        <span class="checkbox checkbox-sm size-4"></span>
                        Deselect All
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th class="text-center">
                                <label class="cursor-pointer">
                                    <input type="checkbox" class="checkbox checkbox-xs" onclick="toggleColumn('view', this.checked)" />
                                    View
                                </label>
                            </th>
                            <th class="text-center">
                                <label class="cursor-pointer">
                                    <input type="checkbox" class="checkbox checkbox-xs" onclick="toggleColumn('create', this.checked)" />
                                    Create
                                </label>
                            </th>
                            <th class="text-center">
                                <label class="cursor-pointer">
                                    <input type="checkbox" class="checkbox checkbox-xs" onclick="toggleColumn('update', this.checked)" />
                                    Update
                                </label>
                            </th>
                            <th class="text-center">
                                <label class="cursor-pointer">
                                    <input type="checkbox" class="checkbox checkbox-xs" onclick="toggleColumn('delete', this.checked)" />
                                    Delete
                                </label>
                            </th>
                            <th class="text-center">
                                <label class="cursor-pointer">
                                    <input type="checkbox" class="checkbox checkbox-xs" onclick="toggleColumn('export', this.checked)" />
                                    Export
                                </label>
                            </th>
                            <th class="text-center">All</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dashboard -->
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--layout-dashboard size-4 text-primary"></span>
                                    <span class="font-medium">Dashboard</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[dashboard.view]" value="1" class="checkbox checkbox-sm perm-view" checked />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[dashboard.export]" value="1" class="checkbox checkbox-sm perm-export" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'dashboard')" />
                            </td>
                        </tr>

                        <!-- Product Management -->
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--package size-4 text-success"></span>
                                    <span class="font-medium">Product Management</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[product.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[product.create]" value="1" class="checkbox checkbox-sm perm-create" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[product.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[product.delete]" value="1" class="checkbox checkbox-sm perm-delete" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[product.export]" value="1" class="checkbox checkbox-sm perm-export" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'product')" />
                            </td>
                        </tr>

                        <!-- Categories (Sub-module) -->
                        <tr>
                            <td class="pl-8">
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--chevron-right size-3 text-base-content/50"></span>
                                    <span>Categories</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[category.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[category.create]" value="1" class="checkbox checkbox-sm perm-create" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[category.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[category.delete]" value="1" class="checkbox checkbox-sm perm-delete" />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'category')" />
                            </td>
                        </tr>

                        <!-- Brands (Sub-module) -->
                        <tr>
                            <td class="pl-8">
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--chevron-right size-3 text-base-content/50"></span>
                                    <span>Brands</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[brand.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[brand.create]" value="1" class="checkbox checkbox-sm perm-create" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[brand.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[brand.delete]" value="1" class="checkbox checkbox-sm perm-delete" />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'brand')" />
                            </td>
                        </tr>

                        <!-- Order Management -->
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--shopping-cart size-4 text-info"></span>
                                    <span class="font-medium">Order Management</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[order.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[order.create]" value="1" class="checkbox checkbox-sm perm-create" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[order.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[order.delete]" value="1" class="checkbox checkbox-sm perm-delete" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[order.export]" value="1" class="checkbox checkbox-sm perm-export" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'order')" />
                            </td>
                        </tr>

                        <!-- Payments (Sub-module) -->
                        <tr>
                            <td class="pl-8">
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--chevron-right size-3 text-base-content/50"></span>
                                    <span>Payments</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[payment.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[payment.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[payment.export]" value="1" class="checkbox checkbox-sm perm-export" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'payment')" />
                            </td>
                        </tr>

                        <!-- Shipments (Sub-module) -->
                        <tr>
                            <td class="pl-8">
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--chevron-right size-3 text-base-content/50"></span>
                                    <span>Shipments</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[shipment.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[shipment.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'shipment')" />
                            </td>
                        </tr>

                        <!-- Customer Management -->
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--users size-4 text-warning"></span>
                                    <span class="font-medium">Customer Management</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[customer.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[customer.create]" value="1" class="checkbox checkbox-sm perm-create" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[customer.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[customer.delete]" value="1" class="checkbox checkbox-sm perm-delete" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[customer.export]" value="1" class="checkbox checkbox-sm perm-export" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'customer')" />
                            </td>
                        </tr>

                        <!-- Marketing -->
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--megaphone size-4 text-secondary"></span>
                                    <span class="font-medium">Marketing</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[marketing.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[marketing.create]" value="1" class="checkbox checkbox-sm perm-create" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[marketing.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[marketing.delete]" value="1" class="checkbox checkbox-sm perm-delete" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[marketing.export]" value="1" class="checkbox checkbox-sm perm-export" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'marketing')" />
                            </td>
                        </tr>

                        <!-- Coupons (Sub-module) -->
                        <tr>
                            <td class="pl-8">
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--chevron-right size-3 text-base-content/50"></span>
                                    <span>Coupons</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[coupon.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[coupon.create]" value="1" class="checkbox checkbox-sm perm-create" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[coupon.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[coupon.delete]" value="1" class="checkbox checkbox-sm perm-delete" />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'coupon')" />
                            </td>
                        </tr>

                        <!-- Reports -->
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--bar-chart size-4 text-accent"></span>
                                    <span class="font-medium">Reports</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[report.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[report.export]" value="1" class="checkbox checkbox-sm perm-export" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'report')" />
                            </td>
                        </tr>

                        <!-- Settings -->
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--settings size-4 text-error"></span>
                                    <span class="font-medium">Settings</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[settings.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[settings.create]" value="1" class="checkbox checkbox-sm perm-create" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[settings.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[settings.delete]" value="1" class="checkbox checkbox-sm perm-delete" />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'settings')" />
                            </td>
                        </tr>

                        <!-- Role Management (Sub-module) -->
                        <tr>
                            <td class="pl-8">
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--chevron-right size-3 text-base-content/50"></span>
                                    <span>Role Management</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[role.view]" value="1" class="checkbox checkbox-sm perm-view" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[role.create]" value="1" class="checkbox checkbox-sm perm-create" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[role.update]" value="1" class="checkbox checkbox-sm perm-update" />
                            </td>
                            <td class="text-center">
                                <input type="checkbox" name="permissions[role.delete]" value="1" class="checkbox checkbox-sm perm-delete" />
                            </td>
                            <td class="text-center">
                                <span class="text-base-content/30">-</span>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" onclick="toggleRow(this, 'role')" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Permission Summary -->
            <div class="mt-6 flex items-center justify-between p-4 bg-base-200 rounded-lg">
                <div>
                    <span class="text-sm">Selected Permissions:</span>
                    <span class="badge badge-primary badge-lg ml-2" id="permission-count">0</span>
                </div>
                <div class="text-sm text-base-content/70">
                    <span class="iconify lucide--info size-4 inline"></span>
                    Permissions will be applied immediately after saving
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex items-center justify-between">
        <a href="{{ route('role.index') }}" class="btn btn-ghost">
            <span class="iconify lucide--arrow-left size-4"></span>
            Back to Roles
        </a>
        <div class="flex gap-2">
            <button type="reset" class="btn btn-ghost" onclick="resetForm()">Reset</button>
            <button type="submit" name="action" value="save_and_new" class="btn btn-outline btn-primary">
                <span class="iconify lucide--save size-4"></span>
                Save & Create Another
            </button>
            <button type="submit" name="action" value="save" class="btn btn-primary">
                <span class="iconify lucide--save size-4"></span>
                Save Role
            </button>
        </div>
    </div>
</form>

<!-- Success Notification -->
<div class="toast toast-end toast-bottom" id="success-toast" style="display:none;">
    <div class="alert alert-success">
        <span class="iconify lucide--check-circle size-5"></span>
        <span>Role created successfully!</span>
    </div>
</div>
@endsection

@section('customjs')
<script>
// Toggle copy from select
function toggleCopyFrom() {
    const checkbox = document.getElementById('copy-permissions-check');
    const selectDiv = document.getElementById('copy-from-select');
    selectDiv.style.display = checkbox.checked ? 'block' : 'none';
}

// Toggle entire row permissions
function toggleRow(checkbox, module) {
    const row = checkbox.closest('tr');
    row.querySelectorAll(`input[name^="permissions[${module}."]`).forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updatePermissionCount();
}

// Toggle entire column
function toggleColumn(permission, checked) {
    document.querySelectorAll('.perm-' + permission).forEach(cb => {
        cb.checked = checked;
    });
    updatePermissionCount();
}

// Select all permissions
function selectAllPermissions() {
    document.querySelectorAll('input[name^="permissions["]').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.querySelectorAll('.checkbox-primary').forEach(checkbox => {
        checkbox.checked = true;
    });
    updatePermissionCount();
}

// Deselect all permissions
function deselectAllPermissions() {
    document.querySelectorAll('input[name^="permissions["]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.querySelectorAll('.checkbox-primary').forEach(checkbox => {
        checkbox.checked = false;
    });
    updatePermissionCount();
}

// Update permission count
function updatePermissionCount() {
    const count = document.querySelectorAll('input[name^="permissions["]:checked').length;
    document.getElementById('permission-count').textContent = count;
}

// Copy permissions from selected role
function copyPermissions(role) {
    if (!role) return;

    // Sample permission templates for different roles
    const roleTemplates = {
        'admin': [
            'dashboard.view', 'dashboard.export',
            'product.view', 'product.create', 'product.update', 'product.export',
            'category.view', 'category.create', 'category.update',
            'brand.view', 'brand.create', 'brand.update',
            'order.view', 'order.create', 'order.update', 'order.export',
            'payment.view', 'payment.update',
            'shipment.view', 'shipment.update',
            'customer.view', 'customer.create', 'customer.update', 'customer.export',
            'marketing.view', 'marketing.create', 'marketing.update',
            'coupon.view', 'coupon.create', 'coupon.update',
            'report.view', 'report.export'
        ],
        'store_manager': [
            'dashboard.view',
            'product.view', 'product.create', 'product.update',
            'category.view', 'category.create', 'category.update',
            'brand.view', 'brand.create', 'brand.update',
            'order.view', 'order.update',
            'payment.view',
            'shipment.view', 'shipment.update',
            'customer.view',
            'report.view', 'report.export'
        ],
        'content_editor': [
            'dashboard.view',
            'product.view', 'product.update',
            'category.view', 'category.update',
            'brand.view', 'brand.update',
            'marketing.view', 'marketing.update'
        ],
        'cs_staff': [
            'dashboard.view',
            'order.view', 'order.update',
            'payment.view',
            'shipment.view',
            'customer.view', 'customer.update'
        ]
    };

    // First, deselect all
    deselectAllPermissions();

    // Then select permissions based on template
    if (roleTemplates[role]) {
        roleTemplates[role].forEach(perm => {
            const checkbox = document.querySelector(`input[name="permissions[${perm}]"]`);
            if (checkbox) checkbox.checked = true;
        });
    }

    updatePermissionCount();
}

// Reset form
function resetForm() {
    deselectAllPermissions();
    document.getElementById('copy-from-select').style.display = 'none';
}

// Add event listeners to all permission checkboxes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name^="permissions["]').forEach(checkbox => {
        checkbox.addEventListener('change', updatePermissionCount);
    });

    // Initialize permission count
    updatePermissionCount();
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Basic validation
    const roleName = document.querySelector('input[name="name"]').value;
    const displayName = document.querySelector('input[name="display_name"]').value;

    if (!roleName || !displayName) {
        alert('Please fill in all required fields');
        return false;
    }

    // Check if at least one permission is selected
    const permissionCount = document.querySelectorAll('input[name^="permissions["]:checked').length;
    if (permissionCount === 0) {
        if (!confirm('No permissions selected. Do you want to create a role without any permissions?')) {
            return false;
        }
    }

    // Show success message (in real app, this would be after API call)
    document.getElementById('success-toast').style.display = 'block';
    setTimeout(() => {
        document.getElementById('success-toast').style.display = 'none';
    }, 3000);

    // In real app, submit the form
    // this.submit();
});
</script>
@endsection