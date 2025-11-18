@extends('layouts.app')

@section('content')

<div class="header header-fixed header-logo-center">
    <span class="header-title">{{ $site->name }}</span>
    <a href="{{ route('home') }}" class="header-icon header-icon-1">
        <i class="fas fa-arrow-left"></i>
    </a>
    <a href="#" data-bs-toggle="modal" data-bs-target="#modalTanggal" class="header-icon header-icon-4">
        <i class="fas fa-calendar-alt"></i>
    </a>
</div>

<div class="page-content pt-5">

    <div class="mb-4">
        <div class="content">
            <form method="GET" action="{{ route('supervisor.site-patroll.show', $site->id) }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 font-600">Filter Session</h5>
                    </div>
                    <input type="hidden" name="date" value="{{ request('date') }}">
                    <div>
                        <select name="session_id"
                                onchange="this.form.submit()"
                                class="form-control font-13"
                                style="padding: 4px 8px;">
                            
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}"
                                    {{ $session && $session->id == $s->id ? 'selected' : '' }}>
                                    Session {{ $s->patroll_code }} ({{ $s->date }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($floors->count() == 0)
        <div class="card card-style text-center mt-4">
            <div class="content">
                <h4>Tidak Ada Data Lantai</h4>
                <p>Belum ada data lantai untuk area Anda.</p>
            </div>
        </div>
    @else

        <div class="row px-3 pt-4">

            @foreach($floors as $floor)

                @php
                    if (!$sessionToday) {
                        $color = '#E74C3C';
                        $statusText = 'Belum Dipatroli';
                        $statusColor = '#E74C3C';

                    } else {
                        $isPatrolled = in_array($floor->id, $patrolledFloors);

                        if ($isPatrolled) {
                            $color = '#2ECC71';
                            $statusText = 'Sudah Dipatroli';
                            $statusColor = '#2ECC71';
                        } else {
                            $color = '#E74C3C';
                            $statusText = 'Belum Dipatroli';
                            $statusColor = '#E74C3C';
                        }
                    }
                @endphp

                <div class="col-6 mb-3">
                    <a href="#"
                        class="d-block text-center p-3"
                        style="border: 1px solid #e5e5e5; border-radius: 8px;">

                        <i class="fas fa-user-shield font-30 mb-2" style="color: {{ $color }};"></i>

                        
                        <h5 class="font-600 mb-1">{{ $floor->name }}</h5>
                        
                        <p class="font-12 mb-2" style="color: {{ $statusColor }}">
                            {{ $statusText }}
                        </p>

                    </a>
                </div>

            @endforeach
        </div>
    @endif
</div>
<!-- Modal Filter Tanggal -->
<div class="modal fade" id="modalTanggal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded">

            <form method="GET" action="{{ route('supervisor.site-patroll.show', $site->id) }}">

                <div class="modal-header">
                    <h5 class="modal-title">Filter Tanggal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Pilih Tanggal</label>
                    <input type="date" name="date" class="form-control"
                        value="{{ request('date', now()->toDateString()) }}">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
