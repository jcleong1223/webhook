@php

    $isEdit = ($mode ?? 'create') === 'edit';

    $name = old('name', $isEdit ? $client->name : '');
    $status = old('status', $isEdit ? $client->status : 'active');
@endphp

<div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input
            type="text" class="form-control @error('name') is-invalid @enderror" id="name"
            name="name" value="{{ $name }}" maxlength="100" required
            placeholder="e.g. POS Backend Service"
        >
        <div class="form-text">
            Human-readable client name. This value is also sent as the
            <code>X-SGDP-Source</code> request header.
        </div>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Allowed IPs --}}
    <div class="mb-3">
        <label for="allowed_ips" class="form-label">Allowed IP Addresses</label>
        <textarea class="form-control @error('allowed_ips') is-invalid @enderror @error('allowed_ips.*') is-invalid @enderror"
            id="allowed_ips"
            name="allowed_ips"
            rows="4"
            placeholder="192.168.1.1&#10;10.0.0.1">{{ is_array(old('allowed_ips', $client->allowed_ips ?? null))
                ? implode("\n", old('allowed_ips', $client->allowed_ips ?? null))
                : old('allowed_ips', $client->allowed_ips ?? '') }}</textarea>

        @error('allowed_ips.*')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Status --}}
    <div class="mb-3">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" id="status"
                name="status" required>
            <option value="active" @selected($status === 'active')>Active</option>
            <option value="inactive" @selected($status === 'inactive')>Inactive</option>
        </select>
        <div class="form-text">
            Only <strong>active</strong> clients can authenticate webhook requests.
        </div>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Auto-generated credentials note --}}
    <div class="alert alert-light border small mb-4">
        <i class="fa-solid fa-circle-info me-1"></i>
        The <strong>Client ID</strong> and <strong>Client Secret</strong> are generated
        automatically when the client is created and shown to you only once.
    </div>