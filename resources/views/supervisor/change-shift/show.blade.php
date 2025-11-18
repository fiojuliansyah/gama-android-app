@extends('layouts.app')

@section('content')

    <div class="header header-fixed header-logo-center">
        <a href="{{ route('supervisor.change-shift.index') }}" class="header-icon header-icon-1">
            <i class="fas fa-arrow-left"></i>
        </a>

        <span class="header-title">User pada Shift {{ $shift->name }}</span>

        <a href="#" data-bs-toggle="modal" data-bs-target="#modalTanggal" class="header-icon header-icon-4">
            <i class="fas fa-calendar-alt"></i>
        </a>
    </div>


    <div class="page-content p-3 pt-5 mt-3">

        <form method="GET" action="{{ route('supervisor.change-shift.show', $shift->id) }}" class="d-flex mb-3">
            <input type="hidden" name="date" value="{{ request('date') }}">

            <input type="text" name="search" class="form-control me-2" placeholder="Cari nama user..."
                value="{{ request('search') }}">

            <button class="btn btn-primary">Cari</button>
        </form>

        @if ($schedules->isEmpty())
            <div class="alert alert-warning text-center">
                Tidak ada user yang terjadwal pada shift ini.
            </div>
        @else
            @foreach ($schedules as $schedule)
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <div>
                        <strong>{{ $schedule->user->name }}</strong><br>
                        <small>{{ $schedule->user->email }}</small><br>
                        <small>Tanggal: {{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}</small>
                    </div>

                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalUbahShift-{{ $schedule->id }}">
                        Ubah Shift
                    </a>
                </div>
                <div class="modal fade" id="modalUbahShift-{{ $schedule->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded">

                            <form method="POST" action="{{ route('supervisor.change-shift.update-schedule') }}">
                                @csrf

                                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

                                <div class="modal-header">
                                    <h5 class="modal-title">Ubah Shift</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <label class="form-label">Pilih Shift Baru</label>
                                    <select name="shift_id" class="form-control" required>
                                        @foreach ($schedule->site->shifts as $s)
                                            <option value="{{ $s->id }}" @if($s->id == $schedule->shift_id) selected @endif>
                                                {{ $s->name }} ({{ substr($s->clock_in, 0, 5) }} -
                                                {{ substr($s->clock_out, 0, 5) }})
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        Batal
                                    </button>

                                    <button type="submit" class="btn btn-primary">
                                        Simpan Perubahan
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>

    <div class="modal fade" id="modalTanggal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded">

                <form method="GET" action="{{ route('supervisor.change-shift.show', $shift->id) }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Filter Tanggal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label">Pilih Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Terapkan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection