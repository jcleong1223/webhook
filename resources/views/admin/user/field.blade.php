@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $formAction = $isEdit
        ? route('admin.user.update', $user->id)
        : route('admin.user.store');

    $name = old('name', $isEdit ? $user->name : '');
    $email = old('email', $isEdit ? $user->email : '');
    $selectedRole = old('role', $isEdit ? $user->roles->first()->name : '');
    $status = old('status', $isEdit ? $user->status : '');

@endphp

<!-- Form -->
<div class="card-body card-bg-secondary">
    <form id="userForm" method="POST" action="{{ $formAction }}" novalidate>
        @csrf
        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input
                type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                name="name" value="{{ $name }}" maxlength="100" required
                placeholder="e.g. POS Backend Service"
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input
                type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                name="email" value="{{ $email }}" maxlength="100" required
                placeholder="e.g. user@example.com"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Old Password --}}
        @if($isEdit)
        <div class="mb-3">
            <label for="old_password" class="form-label">Old Password <span class="text-danger">*</span></label>
            <input
                type="password" class="form-control @error('old_password') is-invalid @enderror" id="old_password"
                name="old_password" value="" maxlength="100" required
                placeholder="Must be at least 10 characters long"
            >
            @error('old_password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @endif

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <input
                type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                name="password" value="" maxlength="100" required
                placeholder="Must be at least 10 characters long"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
            <input
                type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password"
                name="confirm_password" maxlength="100" required
                placeholder="Must match the password"
            >
            @error('confirm_password')
                <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        </div>

        {{-- Role --}}
        <div class="mb-3">
            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
            <select class="form-select {{ $isEdit ? 'bg-secondary' : '' }} @error('role') is-invalid @enderror" id="role"
                    name="role" {{ $isEdit ? 'disabled ' : 'required' }}>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" @selected($selectedRole === $role->name)>{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select class="form-select @error('status') is-invalid @enderror" id="status"
                    name="status" required>
                <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
            </select>
            <div class="form-text">
                Only <strong>active</strong> users can be authenticated to login this portal.
            </div>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </form>
</div>

