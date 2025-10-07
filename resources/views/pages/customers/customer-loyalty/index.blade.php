@extends('layouts.app')

@section('title', 'Customer Loyalty')
@section('page_title', 'Customers')
@section('page_subtitle', 'Loyalty Programs')

@section('content')
<x-page-header
    title="Loyalty Programs & Transactions"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Customers'],
        ['label' => 'Loyalty Programs']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Active Programs"
        :value="number_format($statistics->active_programs ?? 0)"
        subtitle="Running programs"
        icon="gift"
        icon-color="primary"
    />

    <x-stat-card
        title="Total Points Issued"
        :value="number_format($statistics->total_points_issued ?? 0)"
        subtitle="All time"
        icon="coins"
        icon-color="success"
        value-color="text-success"
    />

    <x-stat-card
        title="Points Redeemed"
        :value="number_format($statistics->total_points_redeemed ?? 0)"
        subtitle="All time"
        icon="shopping-bag"
        icon-color="warning"
        value-color="text-warning"
    />

    <x-stat-card
        title="Active Members"
        :value="number_format($statistics->active_members ?? 0)"
        subtitle="With points"
        icon="users"
        icon-color="info"
        value-color="text-info"
    />
</div>

<!-- Programs Section -->
<div class="mt-6">
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 class="card-title">Loyalty Programs</h2>
                @if(hasPermission('customers.loyalty.create'))
                <button type="button" class="btn btn-sm btn-primary" id="addProgramBtn">
                    <span class="iconify lucide--plus size-4"></span>
                    Add Program
                </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Points/Currency</th>
                            <th>Currency/Point</th>
                            <th>Min Redeem</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $program)
                        <tr>
                            <td><span class="font-mono text-sm">{{ $program->code }}</span></td>
                            <td><span class="font-medium">{{ $program->name }}</span></td>
                            <td>{{ number_format($program->points_per_currency, 2) }}</td>
                            <td>Rp {{ number_format($program->currency_per_point, 2) }}</td>
                            <td>{{ number_format($program->min_points_redeem) }} pts</td>
                            <td>
                                @if($program->start_date && $program->end_date)
                                <span class="text-xs">
                                    {{ \Carbon\Carbon::parse($program->start_date)->format('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($program->end_date)->format('d M Y') }}
                                </span>
                                @else
                                <span class="text-xs text-base-content/60">Ongoing</span>
                                @endif
                            </td>
                            <td>
                                <x-badge
                                    :type="$program->is_active ? 'success' : 'error'"
                                    :label="$program->is_active ? 'Active' : 'Inactive'"
                                />
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    @if(hasPermission('customers.loyalty.update'))
                                    <button type="button" class="btn btn-ghost btn-xs edit-program-btn" data-id="{{ $program->id }}">
                                        <span class="iconify lucide--pencil size-4"></span>
                                    </button>
                                    @endif
                                    @if(hasPermission('customers.loyalty.delete'))
                                    <button type="button" class="btn btn-ghost btn-xs text-error delete-program-btn" data-id="{{ $program->id }}">
                                        <span class="iconify lucide--trash-2 size-4"></span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-base-content/60">
                                No programs found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="mt-6">
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 class="card-title">Recent Transactions</h2>
                @if(hasPermission('customers.loyalty.create'))
                <button type="button" class="btn btn-sm btn-primary" id="addTransactionBtn">
                    <span class="iconify lucide--plus size-4"></span>
                    Manual Transaction
                </button>
                @endif
            </div>

            <!-- Filter -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <x-form.select
                    name="transaction_type"
                    label="Type"
                    :value="request('transaction_type')"
                    placeholder="All Types"
                    :options="['earn' => 'Earn', 'redeem' => 'Redeem', 'expire' => 'Expire', 'adjust' => 'Adjust']"
                />
                <x-form.input
                    name="customer_search"
                    label="Customer"
                    :value="request('customer_search')"
                    placeholder="Search by name/email"
                />
                <x-form.input
                    name="date_from"
                    label="From Date"
                    type="date"
                    :value="request('date_from')"
                />
                <x-form.input
                    name="date_to"
                    label="To Date"
                    type="date"
                    :value="request('date_to')"
                />
            </div>

            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Points</th>
                            <th>Balance After</th>
                            <th>Description</th>
                            <th>Expires</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>
                                <span class="text-sm">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}</span>
                            </td>
                            <td>
                                <div>
                                    <div class="font-medium">{{ $transaction->customer_name }}</div>
                                    <div class="text-xs text-base-content/60">{{ $transaction->customer_email }}</div>
                                </div>
                            </td>
                            <td>
                                <x-badge
                                    :type="$transaction->transaction_type === 'earn' ? 'success' : ($transaction->transaction_type === 'redeem' ? 'warning' : ($transaction->transaction_type === 'expire' ? 'error' : 'info'))"
                                    :label="ucfirst($transaction->transaction_type)"
                                />
                            </td>
                            <td>
                                <span class="font-medium {{ $transaction->points > 0 ? 'text-success' : 'text-error' }}">
                                    {{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points) }}
                                </span>
                            </td>
                            <td>
                                <span class="font-medium">{{ number_format($transaction->balance_after) }}</span>
                            </td>
                            <td>
                                <span class="text-sm">{{ Str::limit($transaction->description, 40) ?? '-' }}</span>
                            </td>
                            <td>
                                @if($transaction->expires_at)
                                <span class="text-xs">{{ \Carbon\Carbon::parse($transaction->expires_at)->format('d M Y') }}</span>
                                @else
                                <span class="text-xs text-base-content/60">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/60">
                                No transactions found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <x-pagination-info :paginator="$transactions" />
        </div>
    </div>
</div>

<!-- Add/Edit Program Modal -->
<x-modal id="programModal" title="Add Program">
    <form id="programForm">
        <input type="hidden" id="program_id">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form.input name="name" label="Program Name" required />
            <x-form.input name="code" label="Code" required />
            <x-form.input name="points_per_currency" label="Points per Currency" type="number" step="0.01" required />
            <x-form.input name="currency_per_point" label="Currency per Point" type="number" step="0.01" required />
            <x-form.input name="min_points_redeem" label="Min Points to Redeem" type="number" required />
            <x-form.input name="points_expiry_days" label="Points Expiry (days)" type="number" placeholder="Leave empty for no expiry" />
            <x-form.input name="start_date" label="Start Date" type="date" />
            <x-form.input name="end_date" label="End Date" type="date" />
            <div class="md:col-span-2">
                <x-form.textarea name="description" label="Description" :rows="2" />
            </div>
            <div class="md:col-span-2">
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-2">
                        <input type="checkbox" name="is_active" class="checkbox checkbox-primary" checked />
                        <span class="label-text">Active</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('programModal').close()">Cancel</button>
            <button type="submit" class="btn btn-primary">
                <span class="iconify lucide--save size-4"></span>
                Save
            </button>
        </div>
    </form>
</x-modal>

<!-- Add Transaction Modal -->
<x-modal id="transactionModal" title="Manual Transaction">
    <form id="transactionForm">
        <div class="grid grid-cols-1 gap-4">
            <x-form.select name="customer_id" label="Customer" required :options="[]" id="customer_select" />
            <x-form.select
                name="transaction_type"
                label="Transaction Type"
                required
                :options="['earn' => 'Earn Points', 'redeem' => 'Redeem Points', 'adjust' => 'Adjust Points', 'expire' => 'Expire Points']"
            />
            <x-form.input name="points" label="Points" type="number" required />
            <x-form.textarea name="description" label="Description" :rows="2" required />
        </div>

        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('transactionModal').close()">Cancel</button>
            <button type="submit" class="btn btn-primary">
                <span class="iconify lucide--save size-4"></span>
                Submit
            </button>
        </div>
    </form>
</x-modal>
@endsection

@vite(['resources/js/modules/customers/customer-loyalty/index.js'])
