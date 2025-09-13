@extends('frontend.layouts.layout')

@section('title', 'Scanner Details')

@section('content')
<style>
    /* Temporary page-specific styles — move to your app CSS if you prefer */
    .scanner-card {
        max-width: 640px;
        width: 100%;
        border-radius: 0.75rem;
    }
    .scanner-card .card-header {
        background: linear-gradient(90deg, rgba(99,102,241,0.08), rgba(16,185,129,0.03));
        border-bottom: none;
        border-top-left-radius: .75rem;
        border-top-right-radius: .75rem;
    }
    .label-muted {
        color: #6c757d;
        font-size: .9rem;
    }
    .value-strong {
        font-weight: 600;
        font-size: 1rem;
    }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-center align-items-start">
        <div class="card scanner-card shadow-sm">
            <div class="card-header pb-2">
                <h4 class="mb-0">User Details</h4>
                <small class="text-muted">User scanner details</small>
            </div>

            <div class="card-body">
                {{-- If you prefer a table, use the commented table below instead --}}
                {{--
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th class="label-muted" style="width:30%;">Name</th>
                            <td class="value-strong">{{ $record->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="label-muted">Email</th>
                            <td class="value-strong">{{ $record->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="label-muted">Phone</th>
                            <td class="value-strong">{{ $record->phone ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
                --}}

                {{-- Clean definition-list layout (recommended) --}}
                <dl class="row mb-0">
                    <dt class="col-sm-4 label-muted">Name:</dt>
                    <dd class="col-sm-8 value-strong">{{ $record->name ?? '-' }}</dd>

                    <dt class="col-sm-4 label-muted">Email:</dt>
                    <dd class="col-sm-8 value-strong">{{ $record->email ?? '-' }}</dd>

                    <dt class="col-sm-4 label-muted">Phone:</dt>
                    <dd class="col-sm-8 value-strong">{{ $record->phone ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
