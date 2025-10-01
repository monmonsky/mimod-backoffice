@extends('layouts.app')

@section('title', 'API Tokens')
@section('page_title', 'Settings')
@section('page_subtitle', 'API Token Management')

@section('content')
<div class="container mx-auto p-6 space-y-6">
    <!-- Header with Generate Button -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">API Token Management</h2>
            <p class="text-base-content/60 text-sm mt-1">
                Generate and manage Sanctum API tokens for Settings API access
            </p>
        </div>
        @if(hasPermission('settings.generals.api-tokens.generate'))
            <button
                onclick="showGenerateModal()"
                class="btn btn-primary">
                <span class="iconify lucide--plus size-5"></span>
                Generate New Token
            </button>
        @endif
    </div>

    <!-- API User Info Card -->
    <div class="card bg-info/10 border border-info/20">
        <div class="card-body">
            <div class="flex items-start gap-4">
                <span class="iconify lucide--info size-6 text-info mt-0.5"></span>
                <div class="flex-1">
                    <h3 class="font-semibold text-info mb-2">API User Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-base-content/60">Name</p>
                            <p class="font-medium">{{ $apiUser->name }}</p>
                        </div>
                        <div>
                            <p class="text-base-content/60">Email</p>
                            <p class="font-medium">{{ $apiUser->email }}</p>
                        </div>
                        <div>
                            <p class="text-base-content/60">Total Tokens</p>
                            <p class="font-medium">{{ $tokens->count() }}</p>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-4">
                        <span class="iconify lucide--alert-triangle size-5"></span>
                        <span class="text-sm">
                            This user cannot login via web interface. It's only used for generating API tokens.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tokens List -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Active Tokens</h3>
                @if($tokens->count() > 0 && hasPermission('settings.generals.api-tokens.revoke'))
                <button
                    onclick="revokeAllTokens()"
                    class="btn btn-sm btn-error btn-outline">
                    <span class="iconify lucide--trash-2 size-4"></span>
                    Revoke All
                </button>
                @endif
            </div>

            @if($tokens->count() === 0)
            <div class="text-center py-12">
                <span class="iconify lucide--key size-16 text-base-content/20 mb-4"></span>
                <p class="text-base-content/60">No API tokens yet</p>
                <p class="text-base-content/40 text-sm">Generate your first token to start using the API</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Token Name</th>
                            <th>Abilities</th>
                            <th>Last Used</th>
                            <th>Created</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tokens as $token)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="iconify lucide--key size-4 text-primary"></span>
                                    <span class="font-medium">{{ $token->name }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $abilities = json_decode($token->abilities, true);
                                @endphp
                                @if(in_array('*', $abilities))
                                <span class="badge badge-success badge-sm">Full Access</span>
                                @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach($abilities as $ability)
                                    <span class="badge badge-sm">{{ $ability }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </td>
                            <td>
                                @if($token->last_used_at)
                                <span class="text-sm">{{ \Carbon\Carbon::parse($token->last_used_at)->diffForHumans() }}</span>
                                @else
                                <span class="text-base-content/40 text-sm">Never</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-sm">{{ \Carbon\Carbon::parse($token->created_at)->format('M d, Y H:i') }}</span>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    @if(hasPermission('settings.generals.api-tokens.view'))
                                        <button
                                            onclick="showTokenDetails({{ $token->id }})"
                                            class="btn btn-sm btn-ghost"
                                            title="View Details">
                                            <span class="iconify lucide--eye size-4"></span>
                                        </button>
                                    @endif
                                    @if(hasPermission('settings.generals.api-tokens.revoke'))
                                        <button
                                            onclick="revokeToken({{ $token->id }}, '{{ $token->name }}')"
                                            class="btn btn-sm btn-ghost text-error"
                                            title="Revoke Token">
                                            <span class="iconify lucide--trash-2 size-4"></span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <!-- Usage Instructions -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h3 class="text-lg font-semibold mb-4">How to Use API Tokens</h3>
            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="badge badge-lg badge-primary">1</div>
                    <div class="flex-1">
                        <h4 class="font-medium mb-1">Generate Token</h4>
                        <p class="text-sm text-base-content/60">Click "Generate New Token" button and provide a token name</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="badge badge-lg badge-primary">2</div>
                    <div class="flex-1">
                        <h4 class="font-medium mb-1">Copy Token</h4>
                        <p class="text-sm text-base-content/60">Save the generated token immediately. You won't be able to see it again!</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="badge badge-lg badge-primary">3</div>
                    <div class="flex-1">
                        <h4 class="font-medium mb-1">Use in API Requests</h4>
                        <p class="text-sm text-base-content/60 mb-2">Include token in Authorization header:</p>
                        <div class="mockup-code text-xs">
                            <pre data-prefix="$"><code>curl -X GET "{{ url('/api/settings/general') }}" \</code></pre>
                            <pre data-prefix=""><code>  -H "Authorization: Bearer YOUR_TOKEN_HERE" \</code></pre>
                            <pre data-prefix=""><code>  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Token Modal -->
<dialog id="generateTokenModal" class="modal">
    <div class="modal-box max-w-2xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        <h3 class="font-bold text-lg mb-4">Generate New API Token</h3>
        </form>

        <form id="generateTokenForm" class="space-y-4">
            @csrf
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Token Name</span>
                </label>
                <input
                    type="text"
                    name="token_name"
                    placeholder="e.g., settings-api-token"
                    class="input input-bordered w-full"
                    required />
                <label class="label">
                    <span class="label-text-alt text-base-content/60">Give your token a descriptive name</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Abilities (Optional)</span>
                </label>
                <div class="space-y-2">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="radio" name="ability_type" value="full" class="radio radio-primary" checked />
                        <div>
                            <span class="label-text font-medium">Full Access</span>
                            <p class="text-xs text-base-content/60">Can access all Settings API endpoints</p>
                        </div>
                    </label>
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="radio" name="ability_type" value="readonly" class="radio radio-primary" />
                        <div>
                            <span class="label-text font-medium">Read Only</span>
                            <p class="text-xs text-base-content/60">Can only read settings (GET requests)</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="modal-action">
                <button type="button" onclick="document.getElementById('generateTokenModal').close()" class="btn btn-ghost">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <span class="iconify lucide--key size-5"></span>
                    Generate Token
                </button>
            </div>
    </div>
        </form>
</dialog>

<!-- Token Success Modal -->
<dialog id="tokenSuccessModal" class="modal">
    <div class="modal-box max-w-3xl">
        <h3 class="font-bold text-lg mb-4 text-success flex items-center gap-2">
            <span class="iconify lucide--check-circle size-6"></span>
            Token Generated Successfully!
        </h3>

        <div class="alert alert-warning mb-4">
            <span class="iconify lucide--alert-triangle size-5"></span>
            <span class="text-sm">
                <strong>Important:</strong> Copy this token now. You won't be able to see it again!
            </span>
        </div>

        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Your API Token:</span>
            </label>
            <div class="flex gap-2">
                <input
                    type="text"
                    id="generatedToken"
                    class="input input-bordered flex-1 font-mono text-sm"
                    readonly />
                <button
                    onclick="copyToken()"
                    class="btn btn-square btn-primary">
                    <span class="iconify lucide--copy size-5"></span>
                </button>
            </div>
        </div>

        <div class="mt-6 p-4 bg-base-200 rounded-lg">
            <p class="text-sm font-medium mb-2">Example Usage:</p>
            <div class="mockup-code text-xs">
                <pre data-prefix="$"><code>curl -X GET "{{ url('/api/settings/general') }}" \</code></pre>
                <pre data-prefix=""><code>  -H "Authorization: Bearer <span id="tokenExample"></span>" \</code></pre>
                <pre data-prefix=""><code>  -H "Accept: application/json"</code></pre>
            </div>
        </div>

        <div class="modal-action">
            <button onclick="closeSuccessModal()" class="btn btn-primary">
                Done
            </button>
        </div>
    </div>
</dialog>

<!-- Token Details Modal -->
<dialog id="tokenDetailsModal" class="modal">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        <h3 class="font-bold text-lg mb-4">Token Details</h3>
        </form>

        <div id="tokenDetailsContent" class="space-y-3">
            <!-- Will be populated by JavaScript -->
        </div>

        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Close</button>
        </div>
            </form>
    </div>
</dialog>

@endsection

@section('customjs')
@vite(['resources/js/modules/settings/generals/api-tokens.js'])
@endsection
