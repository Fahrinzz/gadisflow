@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="page-head"><h3>Company Settings</h3></div>

<form method="POST" action="{{ route('settings.update') }}">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><strong>Company Information</strong> <span class="text-muted small">(shown in the header of every document)</span></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $settings->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Registration No.</label>
                        <input type="text" name="reg_no" class="form-control" value="{{ old('reg_no', $settings->reg_no) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $settings->address) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" value="{{ old('email', $settings->email) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone / Contact</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $settings->phone) }}">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Bank Info (payable to)</label>
                        <input type="text" name="bank_info" class="form-control" value="{{ old('bank_info', $settings->bank_info) }}">
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><strong>Default Terms & Conditions</strong></div>
                <div class="card-body">
                    <textarea name="default_terms" class="form-control" rows="4">{{ old('default_terms', $settings->default_terms) }}</textarea>
                    <div class="form-text">This text is filled in automatically when creating a new document (still editable).</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><strong>Numbering</strong></div>
                <div class="card-body">
                    <label class="form-label">Next Number</label>
                    <input type="number" name="next_number" class="form-control" value="{{ old('next_number', $settings->next_number) }}" required>
                    <div class="form-text">E.g.: {{ $settings->next_number }} → Q0{{ $settings->next_number }}, Inv{{ $settings->next_number }}, DO{{ $settings->next_number }}.</div>
                </div>
            </div>
            <button type="submit" class="btn btn-dark w-100"><i class="bi bi-check-lg"></i> Save Settings</button>
        </div>
    </div>
</form>
@endsection
