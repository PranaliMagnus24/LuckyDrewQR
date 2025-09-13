@extends('frontend.layouts.layout')

@section('title', 'Scanner Form')

@section('content')
<style>
    /* Page-specific styles — move to your main CSS if you prefer */
    .scanner-card {
        max-width: 640px;
        width: 100%;
        border-radius: 0.75rem;
    }
    .scanner-card .card-header {
        background: linear-gradient(90deg, rgba(99,102,241,0.06), rgba(16,185,129,0.02));
        border-bottom: none;
        border-top-left-radius: .75rem;
        border-top-right-radius: .75rem;
    }
    .form-help {
        font-size: .85rem;
        color: #6c757d;
    }
</style>

<div class="container py-5">
    <!-- horizontally center card; to vertically center too, add `min-vh-100 d-flex align-items-center` to .container -->
    <div class="d-flex justify-content-center">
        <div class="card scanner-card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Registration Form</h4>
                <small class="text-muted">Fill the details to register the scanner</small>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('scanner.submit') }}" novalidate>
                    @csrf
                    <input type="hidden" name="unique_id" value="{{ $unique_id }}">

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input id="name" type="text" name="name" placeholder="Enter your name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" aria-describedby="nameHelp">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input id="email" type="email" name="email" placeholder="Enter your email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                        <input id="phone" type="text" name="phone" placeholder="Enter your phone number" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" inputmode="tel" aria-describedby="phoneHelp">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
