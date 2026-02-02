<div class="row">
    <input type="hidden" name="id" value="{{ $customer->id ?? '' }}">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            placeholder="e.g., John Doe" value="{{ old('name', $customer->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label fw-semibold">Email</label>
        <div class="input-group">
            <input type="email" class="form-control" id="email" name="email"
                placeholder="e.g., john@example.com" value="{{ old('email', $customer->email ?? '') }}" readonly
                style="background-color: #e9ecef !important; cursor: not-allowed;">
            <span class="input-group-text">
                <svg fill="#6B7280" width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                    enable-background="new 0 0 24 24">
                    <path
                        d="M17,9V7c0-2.8-2.2-5-5-5S7,4.2,7,7v2c-1.7,0-3,1.3-3,3v7c0,1.7,1.3,3,3,3h10c1.7,0,3-1.3,3-3v-7C20,10.3,18.7,9,17,9z M9,7c0-1.7,1.3-3,3-3s3,1.3,3,3v2H9V7z" />
                </svg>
            </span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label fw-semibold">Phone</label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
            placeholder="e.g., +1 234 567 8900" value="{{ old('phone', $customer->phone ?? '') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label fw-semibold">Status</label>
        <div class="form-check form-switch">
            <input type="hidden" name="status" value="0">
            <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch"
                style="width: 3rem; height: 1.5rem;" {{ old('status', $customer->status ?? 1) ? 'checked' : '' }}>
            <label class="form-check-label ms-2" for="statusSwitch">Active</label>
        </div>
    </div>
</div>
