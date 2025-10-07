@extends('layouts.app')

@section('title', 'Customer Reviews')
@section('page_title', 'Customers')
@section('page_subtitle', 'Product Reviews')

@section('content')
<x-page-header
    title="Customer Reviews"
    :breadcrumbs="[
        ['label' => 'Nexus', 'url' => route('dashboard')],
        ['label' => 'Customers'],
        ['label' => 'Reviews']
    ]"
/>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card
        title="Total Reviews"
        :value="number_format($statistics->total_reviews ?? 0)"
        subtitle="All time"
        icon="message-square"
        icon-color="primary"
    />

    <x-stat-card
        title="Pending Approval"
        :value="number_format($statistics->pending_reviews ?? 0)"
        subtitle="Awaiting review"
        icon="clock"
        icon-color="warning"
        value-color="text-warning"
    />

    <x-stat-card
        title="Approved Reviews"
        :value="number_format($statistics->approved_reviews ?? 0)"
        subtitle="Published"
        icon="check-circle"
        icon-color="success"
        value-color="text-success"
    />

    <x-stat-card
        title="Average Rating"
        :value="number_format($statistics->average_rating ?? 0, 1)"
        subtitle="Overall"
        icon="star"
        icon-color="info"
        value-color="text-info"
    />
</div>

<!-- Filter Section -->
<div class="mt-6">
    <x-filter-section title="Filter Reviews" :action="route('customers.reviews.index')">
        <x-slot name="filters">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-form.input
                    name="product_name"
                    label="Product"
                    :value="request('product_name')"
                    placeholder="Search by product"
                />

                <x-form.input
                    name="customer_name"
                    label="Customer"
                    :value="request('customer_name')"
                    placeholder="Search by customer"
                />

                <x-form.select
                    name="rating"
                    label="Rating"
                    :value="request('rating')"
                    placeholder="All Ratings"
                    :options="['5' => '5 Stars', '4' => '4 Stars', '3' => '3 Stars', '2' => '2 Stars', '1' => '1 Star']"
                />

                <x-form.select
                    name="is_approved"
                    label="Status"
                    :value="request('is_approved')"
                    placeholder="All Status"
                    :options="['1' => 'Approved', '0' => 'Pending']"
                />

                <x-form.select
                    name="is_verified"
                    label="Purchase"
                    :value="request('is_verified')"
                    placeholder="All"
                    :options="['1' => 'Verified Purchase', '0' => 'Not Verified']"
                />
            </div>
        </x-slot>

        <x-slot name="actions">
            <button type="submit" class="btn btn-sm btn-primary">
                <span class="iconify lucide--search size-4"></span>
                Apply Filter
            </button>
            @if(request()->hasAny(['product_name', 'customer_name', 'rating', 'is_approved', 'is_verified']))
            <a href="{{ route('customers.reviews.index') }}" class="btn btn-sm btn-ghost">
                <span class="iconify lucide--x size-4"></span>
                Clear All
            </a>
            @endif
        </x-slot>
    </x-filter-section>
</div>

<!-- Reviews Table -->
<div class="mt-6">
    <div class="bg-base-100 card shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table" id="reviewsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr class="hover">
                            <td>
                                <span class="text-sm">{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div>
                                        <div class="font-medium">{{ $review->product_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="font-medium">{{ $review->customer_name }}</div>
                                    <div class="text-xs text-base-content/60">{{ $review->customer_email }}</div>
                                    @if($review->is_verified_purchase)
                                    <div class="badge badge-xs badge-success mt-1">Verified Purchase</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="iconify lucide--star size-4 {{ $i <= $review->rating ? 'text-warning' : 'text-base-300' }}"></span>
                                    @endfor
                                    <span class="text-sm ml-1">({{ $review->rating }})</span>
                                </div>
                            </td>
                            <td>
                                <div class="max-w-md">
                                    @if($review->title)
                                    <div class="font-medium text-sm">{{ $review->title }}</div>
                                    @endif
                                    <div class="text-sm text-base-content/70">
                                        {{ Str::limit($review->comment, 100) }}
                                    </div>
                                    @if($review->admin_response)
                                    <div class="mt-2 p-2 bg-base-200 rounded text-xs">
                                        <strong>Admin Response:</strong> {{ Str::limit($review->admin_response, 80) }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <x-badge
                                    :type="$review->is_approved ? 'success' : 'warning'"
                                    :label="$review->is_approved ? 'Approved' : 'Pending'"
                                />
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    @if(hasPermission('customers.reviews.view'))
                                    <button type="button" class="btn btn-ghost btn-xs view-review-btn" data-id="{{ $review->id }}">
                                        <span class="iconify lucide--eye size-4"></span>
                                        View
                                    </button>
                                    @endif

                                    @if(!$review->is_approved && hasPermission('customers.reviews.approve'))
                                    <button type="button" class="btn btn-ghost btn-xs text-success approve-review-btn" data-id="{{ $review->id }}">
                                        <span class="iconify lucide--check size-4"></span>
                                        Approve
                                    </button>
                                    @endif

                                    @if(hasPermission('customers.reviews.delete'))
                                    <button type="button" class="btn btn-ghost btn-xs text-error delete-review-btn" data-id="{{ $review->id }}">
                                        <span class="iconify lucide--trash-2 size-4"></span>
                                        Delete
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/60">
                                <span class="iconify lucide--message-square size-12 mx-auto block mb-2 opacity-20"></span>
                                No reviews found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info & Links -->
            <x-pagination-info :paginator="$reviews" />
        </div>
    </div>
</div>

<!-- View Review Modal -->
<x-modal id="viewReviewModal" title="Review Details" size="lg">
    <div id="reviewDetails">
        <div class="flex items-center justify-center py-8">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
    </div>

    <x-slot name="footer">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('viewReviewModal').close()">Close</button>
        <button type="button" class="btn btn-primary" id="respondBtn">
            <span class="iconify lucide--message-circle size-4"></span>
            Respond
        </button>
    </x-slot>
</x-modal>

<!-- Respond Modal -->
<x-modal id="respondModal" title="Respond to Review">
    <form id="respondForm">
        <input type="hidden" id="review_id">

        <div class="space-y-4">
            <div id="reviewSummary" class="p-3 bg-base-200 rounded">
                <!-- Review summary will be loaded here -->
            </div>

            <x-form.textarea
                name="admin_response"
                label="Your Response"
                :rows="4"
                required
                placeholder="Write your response to this review..."
            />
        </div>

        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('respondModal').close()">Cancel</button>
            <button type="submit" class="btn btn-primary">
                <span class="iconify lucide--send size-4"></span>
                Submit Response
            </button>
        </div>
    </form>
</x-modal>
@endsection

@vite(['resources/js/modules/customers/customer-reviews/index.js'])
