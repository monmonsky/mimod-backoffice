@extends('layouts.app')

@section('title', 'Product Performance')
@section('page_title', 'Reports')
@section('page_subtitle', 'Product Performance')

@section('content')
<div class="flex items-center justify-between">
    <p class="text-lg font-medium">Product Performance Report</p>
    <div class="breadcrumbs hidden p-0 text-sm sm:inline">
        <ul>
            <li><a href="{{ route('dashboard') }}">Nexus</a></li>
            <li>Reports</li>
            <li class="opacity-80">Product Performance</li>
        </ul>
    </div>
</div>

<!-- Filters -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Period</span>
                </label>
                <select class="select select-bordered select-sm">
                    <option selected>Last 30 Days</option>
                    <option>Last 90 Days</option>
                    <option>This Year</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Category</span>
                </label>
                <select class="select select-bordered select-sm">
                    <option selected>All Categories</option>
                    <option>Clothing</option>
                    <option>Footwear</option>
                    <option>Accessories</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Sort By</span>
                </label>
                <select class="select select-bordered select-sm">
                    <option selected>Revenue</option>
                    <option>Units Sold</option>
                    <option>Growth Rate</option>
                    <option>Profit Margin</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Status</span>
                </label>
                <select class="select select-bordered select-sm">
                    <option selected>All Products</option>
                    <option>In Stock</option>
                    <option>Low Stock</option>
                    <option>Out of Stock</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">&nbsp;</span>
                </label>
                <button class="btn btn-primary btn-sm">
                    <span class="iconify lucide--search size-4"></span>
                    Search
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Total Products</p>
                    <h3 class="text-2xl font-bold mt-1">248</h3>
                    <p class="text-xs text-success mt-2">+12 this month</p>
                </div>
                <div class="bg-primary/10 rounded-full p-3">
                    <span class="iconify lucide--package size-6 text-primary"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Active Products</p>
                    <h3 class="text-2xl font-bold mt-1">215</h3>
                    <p class="text-xs text-info mt-2">86.7% of total</p>
                </div>
                <div class="bg-info/10 rounded-full p-3">
                    <span class="iconify lucide--check-circle-2 size-6 text-info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Low Stock</p>
                    <h3 class="text-2xl font-bold mt-1">23</h3>
                    <p class="text-xs text-warning mt-2">Need restock</p>
                </div>
                <div class="bg-warning/10 rounded-full p-3">
                    <span class="iconify lucide--alert-triangle size-6 text-warning"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-base-content/60">Out of Stock</p>
                    <h3 class="text-2xl font-bold mt-1">10</h3>
                    <p class="text-xs text-error mt-2">Requires action</p>
                </div>
                <div class="bg-error/10 rounded-full p-3">
                    <span class="iconify lucide--x-circle size-6 text-error"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top & Bottom Performers -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top Performers -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4 flex items-center gap-2">
                <span class="iconify lucide--trending-up text-success"></span>
                Top Performers
            </h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 bg-success/5 rounded-lg">
                    <div class="badge badge-success">1</div>
                    <div class="flex-1">
                        <p class="font-medium">Kids T-Shirt Premium</p>
                        <p class="text-sm text-base-content/60">342 units sold</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-success">Rp 12.5M</p>
                        <p class="text-xs text-success">+15%</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-success/5 rounded-lg">
                    <div class="badge badge-success">2</div>
                    <div class="flex-1">
                        <p class="font-medium">Kids Sneakers</p>
                        <p class="text-sm text-base-content/60">287 units sold</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-success">Rp 10.2M</p>
                        <p class="text-xs text-success">+8%</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-success/5 rounded-lg">
                    <div class="badge badge-success">3</div>
                    <div class="flex-1">
                        <p class="font-medium">Kids Jacket</p>
                        <p class="text-sm text-base-content/60">198 units sold</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-success">Rp 7.8M</p>
                        <p class="text-xs text-success">+12%</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-success/5 rounded-lg">
                    <div class="badge badge-success">4</div>
                    <div class="flex-1">
                        <p class="font-medium">School Bag</p>
                        <p class="text-sm text-base-content/60">156 units sold</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-success">Rp 5.4M</p>
                        <p class="text-xs text-success">+5%</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-success/5 rounded-lg">
                    <div class="badge badge-success">5</div>
                    <div class="flex-1">
                        <p class="font-medium">Baby Socks Set</p>
                        <p class="text-sm text-base-content/60">145 units sold</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-success">Rp 4.8M</p>
                        <p class="text-xs text-success">+18%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Performers -->
    <div class="bg-base-100 card shadow">
        <div class="card-body">
            <h3 class="card-title text-base mb-4 flex items-center gap-2">
                <span class="iconify lucide--trending-down text-error"></span>
                Need Attention
            </h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 bg-error/5 rounded-lg">
                    <div class="badge badge-error">!</div>
                    <div class="flex-1">
                        <p class="font-medium">Winter Coat</p>
                        <p class="text-sm text-base-content/60">12 units sold</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-error">Rp 1.2M</p>
                        <p class="text-xs text-error">-45%</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-error/5 rounded-lg">
                    <div class="badge badge-error">!</div>
                    <div class="flex-1">
                        <p class="font-medium">Rain Boots</p>
                        <p class="text-sm text-base-content/60">18 units sold</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-error">Rp 1.5M</p>
                        <p class="text-xs text-error">-38%</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-warning/5 rounded-lg">
                    <div class="badge badge-warning">!</div>
                    <div class="flex-1">
                        <p class="font-medium">Sun Hat</p>
                        <p class="text-sm text-base-content/60">28 units sold</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-warning">Rp 980K</p>
                        <p class="text-xs text-warning">-12%</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-warning/5 rounded-lg">
                    <div class="badge badge-warning">!</div>
                    <div class="flex-1">
                        <p class="font-medium">Swim Suit</p>
                        <p class="text-sm text-base-content/60">35 units sold</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-warning">Rp 1.8M</p>
                        <p class="text-xs text-warning">-8%</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-warning/5 rounded-lg">
                    <div class="badge badge-warning">!</div>
                    <div class="flex-1">
                        <p class="font-medium">Formal Shoes</p>
                        <p class="text-sm text-base-content/60">42 units sold</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-warning">Rp 2.1M</p>
                        <p class="text-xs text-warning">-5%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Performance Table -->
<div class="mt-6 bg-base-100 card shadow">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-title text-base">All Products Performance</h3>
            <div class="flex gap-2">
                <button class="btn btn-ghost btn-sm">
                    <span class="iconify lucide--download size-4"></span>
                    Export
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>SKU</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                        <th>Avg Price</th>
                        <th>Stock</th>
                        <th>Growth</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-primary/10 flex items-center justify-center">
                                        <span class="iconify lucide--shirt size-5 text-primary"></span>
                                    </div>
                                </div>
                                <span class="font-medium">Kids T-Shirt Premium</span>
                            </div>
                        </td>
                        <td>Clothing</td>
                        <td class="font-mono text-sm">KTS-001</td>
                        <td>342</td>
                        <td class="font-semibold">Rp 12.5M</td>
                        <td>Rp 36.5K</td>
                        <td>156</td>
                        <td><span class="text-success font-medium">+15%</span></td>
                        <td><span class="badge badge-success badge-sm">In Stock</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-info/10 flex items-center justify-center">
                                        <span class="iconify lucide--footprints size-5 text-info"></span>
                                    </div>
                                </div>
                                <span class="font-medium">Kids Sneakers</span>
                            </div>
                        </td>
                        <td>Footwear</td>
                        <td class="font-mono text-sm">KSN-002</td>
                        <td>287</td>
                        <td class="font-semibold">Rp 10.2M</td>
                        <td>Rp 35.5K</td>
                        <td>89</td>
                        <td><span class="text-success font-medium">+8%</span></td>
                        <td><span class="badge badge-success badge-sm">In Stock</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-warning/10 flex items-center justify-center">
                                        <span class="iconify lucide--package size-5 text-warning"></span>
                                    </div>
                                </div>
                                <span class="font-medium">Baby Romper Set</span>
                            </div>
                        </td>
                        <td>Clothing</td>
                        <td class="font-mono text-sm">BRS-003</td>
                        <td>213</td>
                        <td class="font-semibold">Rp 8.9M</td>
                        <td>Rp 41.8K</td>
                        <td>45</td>
                        <td><span class="text-error font-medium">-3%</span></td>
                        <td><span class="badge badge-warning badge-sm">Low Stock</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-success/10 flex items-center justify-center">
                                        <span class="iconify lucide--star size-5 text-success"></span>
                                    </div>
                                </div>
                                <span class="font-medium">Kids Jacket</span>
                            </div>
                        </td>
                        <td>Clothing</td>
                        <td class="font-mono text-sm">KJK-004</td>
                        <td>198</td>
                        <td class="font-semibold">Rp 7.8M</td>
                        <td>Rp 39.4K</td>
                        <td>67</td>
                        <td><span class="text-success font-medium">+12%</span></td>
                        <td><span class="badge badge-success badge-sm">In Stock</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-error/10 flex items-center justify-center">
                                        <span class="iconify lucide--shopping-bag size-5 text-error"></span>
                                    </div>
                                </div>
                                <span class="font-medium">School Bag</span>
                            </div>
                        </td>
                        <td>Accessories</td>
                        <td class="font-mono text-sm">SBG-005</td>
                        <td>156</td>
                        <td class="font-semibold">Rp 5.4M</td>
                        <td>Rp 34.6K</td>
                        <td>12</td>
                        <td><span class="text-success font-medium">+5%</span></td>
                        <td><span class="badge badge-warning badge-sm">Low Stock</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div class="text-sm text-base-content/60">
                Showing 1 to 5 of 248 products
            </div>
            <div class="join">
                <button class="join-item btn btn-sm">«</button>
                <button class="join-item btn btn-sm btn-active">1</button>
                <button class="join-item btn btn-sm">2</button>
                <button class="join-item btn btn-sm">3</button>
                <button class="join-item btn btn-sm">»</button>
            </div>
        </div>
    </div>
</div>
@endsection