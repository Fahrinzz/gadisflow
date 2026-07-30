@extends('layouts.app')

@section('title', 'Log In')

@push('head')
<style>
    body{ background:linear-gradient(135deg,#ffd9e2 0%, #f6f1f3 55%); min-height:100vh; }
    .login-wrap{ min-height:100vh; display:flex; align-items:center; }
    .login-card{ border-radius:18px; overflow:hidden; }
    .login-logo{ width:96px; height:96px; border-radius:20px; background:var(--pink); color:var(--maroon);
        display:flex; align-items:center; justify-content:center; font-weight:900; font-style:italic;
        font-size:1.5rem; line-height:1; margin:0 auto 1rem; box-shadow:0 6px 18px rgba(110,32,51,.18); }
    .login-logo span{ display:block; text-align:center; }
</style>
@endpush

@section('content')
<div class="login-wrap">
    <div class="row justify-content-center w-100">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow login-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="login-logo"><span>GADIS<br>KREATIF</span></div>
                    <h4 class="mb-1 text-center fw-bold" style="color:var(--maroon)">{{ config('app.name') }}</h4>
                    <p class="text-muted text-center mb-4">Please log in to continue</p>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>
                        <div class="form-check mb-4">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-box-arrow-in-right me-1"></i> Log In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
