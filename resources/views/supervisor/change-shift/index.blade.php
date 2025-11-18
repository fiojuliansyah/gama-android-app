@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <a href="#" class="header-title">Change Shift</a>
    <a href="{{ route('home') }}" class="header-icon header-icon-1">
        <i class="fas fa-arrow-left"></i>
    </a>
</div>

<div class="page-content pt-4 mt-4">

    <div class="mx-3 mt-3">

        {{-- FILTER SITE --}}
        <form method="GET" class="mb-3">
            <label class="small fw-bold">Filter Site</label>
            <select name="site_id" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Site</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}" {{ $selectedSiteId == $site->id ? 'selected' : '' }}>
                        {{ $site->name }}
                    </option>
                @endforeach
            </select>
        </form>

    </div>

    @if ($shifts->isEmpty())
        <div class="alert alert-warning mx-3 mt-3 text-center">
            Tidak ada shift pada site ini.
        </div>
    @else
        <div class="mx-3 p-3">

            <h4 class="mb-3">Daftar Shift</h4>

            @foreach ($shifts as $shift)
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <div>
                        <strong>{{ $shift->name }}</strong><br>
                        <small>Kode: {{ $shift->shift_code }}</small><br>
                        <small>Masuk: {{ substr($shift->clock_in, 0, 5) }}</small><br>
                        <small>Pulang: {{ substr($shift->clock_out, 0, 5) }}</small><br>
                    </div>

                    <a href="{{ route('supervisor.change-shift.show', $shift->id) }}" class="btn btn-primary btn-sm">
                        Pilih
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
