@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Profile Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Profile Information</h6>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="{{ route('profile.update') }}">
                                        @csrf
                                        @method('patch')

                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                            @error('name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                                            @error('email')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror

                                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                                <div class="mt-2">
                                                    <p class="small text-muted">
                                                        Your email address is unverified.
                                                        <a href="{{ route('verification.send') }}" class="text-decoration-none">Click here to re-send the verification email.</a>
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                                            <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $user->whatsapp_number) }}" placeholder="e.g., 03001234567" autocomplete="tel">
                                            <div class="form-text">Enter your WhatsApp number to receive notifications via WhatsApp.</div>
                                            @error('whatsapp_number')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-lg me-1"></i>Save Changes
                                            </button>
                                        </div>

                                        @if (session('status') === 'profile-updated')
                                            <div class="alert alert-success mt-3">
                                                <i class="bi bi-check-circle me-1"></i>Profile updated successfully!
                                            </div>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">User Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar me-3">{{ strtoupper(substr(auth()->user()->name ?? 'AU', 0, 2)) }}</div>
                                        <div>
                                            <div class="fw-bold">{{ auth()->user()->name }}</div>
                                            <div class="small text-muted">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                    <div class="small">
                                        <div class="mb-1"><strong>Role:</strong> {{ ucfirst(auth()->user()->role) }}</div>
                                        <div class="mb-1"><strong>Status:</strong> {{ ucfirst(auth()->user()->status) }}</div>
                                        @if(auth()->user()->whatsapp_number)
                                            <div><strong>WhatsApp:</strong> {{ auth()->user()->whatsapp_number }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--navy-600), var(--navy-800));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 18px;
}
</style>
@endsection
