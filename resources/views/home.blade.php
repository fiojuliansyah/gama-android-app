@extends('layouts.master')
@section('content')
    <div class="page-content pt-3">
        <div class="content">
            <div class="d-flex">
                <div class="align-self-center">
                    <a href="{{ route('setting') }}">
                        <img src="{{ Auth::user()->profile->avatar_url }}"
                            class="rounded-circle" width="43">
                    </a>
                </div>
                <div class="align-self-center">
                    <h4 class="ps-2 pt-2">{{ Auth::user()->name }}</h4>
                    <p class="ps-2 mt-n2 mb-0 font-11">{{ Auth::user()->employee_nik ?? 'Admin' }} (
                        @foreach (Auth::user()->getRoleNames() as $role)
                            {{ $role }}
                        @endforeach)
                    </p>
                </div>
                <div class="ms-auto align-self-center">
                    <a href="{{ route('findings-reports.index') }}"
                        class="btn btn-full btn-xs rounded-s bg-highlight text-uppercase font-900">
                        SOS
                    </a>
                </div>
            </div>
        </div>
        <div class="content text-center">
            <div class="page-title page-title-small">
                <img src="/assets/images/gama-trans.png" width="200px">
            </div>
        </div>
        <div class="content">
            <div class="row mb-n2">
                <div class="col-4 pe-2">
                    <div class="card card-style mx-0 mb-3">
                        <div class="p-3">
                            <h4 class="font-700 text-uppercase font-10 opacity-50 mt-n2">Absensi</h4>
                            <h1 class="font-700 font-34 color-highlight  mb-0">{{ $attendanceCount }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-4 ps-2">
                    <div class="card card-style mx-0 mb-3">
                        <div class="p-3">
                            <h4 class="font-700 text-uppercase font-10 opacity-50 mt-n2">Telat</h4>
                            <h1 class="font-700 font-34 color-highlight mb-0">{{ $lateCount }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-4 ps-2">
                    <div class="card card-style mx-0 mb-3">
                        <div class="p-3">
                            <h4 class="font-700 text-uppercase font-10 opacity-50 mt-n2">Alpha</h4>
                            <h1 class="font-700 font-34 color-highlight mb-0">{{ $alphaCount }}</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-n2">
                <div class="col-4 ps-2 pe-2">
                    <div class="card card-style mx-0 mb-3">
                        <div class="p-3">
                            <h4 class="font-700 text-uppercase font-10 opacity-50 mt-n2">Lembur</h4>
                            <h1 class="font-700 font-34 color-highlight mb-0">{{ $overtimeCount }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-4 pe-2">
                    <div class="card card-style mx-0 mb-3">
                        <div class="p-3">
                            <h4 class="font-700 text-uppercase font-10 opacity-50 mt-n2">Cuti</h4>
                            <h1 class="font-700 font-34 color-highlight mb-0">{{ $leaveCount }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-4 ps-2 pe-2">
                    <div class="card card-style mx-0 mb-3">
                        <div class="p-3">
                            <h4 class="font-700 text-uppercase font-10 opacity-50 mt-n2">Izin</h4>
                            <h1 class="font-700 font-34 color-highlight mb-0">{{ $permitCount }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-style">
            <div class="content">
                <div class="d-flex mb-4">
                    <div class="align-self-center w-100">
                        <h4 class="text-center">Jadwal</h4>
                        @if ($schedule)
                            @if ($schedule->type == 'off')
                                <h6 class="text-center">LIBUR</h6>
                            @else
                                <h3 class="text-center">{{ $schedule->clock_in ?? '' }} - {{ $schedule->clock_out ?? '' }}
                                </h3>
                            @endif
                        @else
                            <h6 class="text-center">Tidak ada jadwal hari ini.</h6>
                        @endif
                        <div class="text-center">
                            <strong class="text-center"><i class="fas fa-map-marker-alt"></i>&nbsp; Jakarta</strong>
                            <br>
                            <strong id="clock"></strong> &nbsp; | &nbsp;
                            <strong id="date"></strong>
                        </div>
                        <p class="mb-0 opacity-60 line-height-s font-12">

                        </p>
                    </div>
                </div>
                <div class="align-self-center">
                    @if ($latestAttendance && $latestAttendance->clock_in && $latestAttendance->clock_out != null)
                    @elseif (!$latestAttendance || $latestAttendance->clock_in == null)
                        <a href="{{ route('attendance.index') }}"
                            class="btn btn-full btn-sm rounded-sm bg-highlight font-700 text-uppercase mt-3">
                            <i class="fas fa-fingerprint"></i>
                            Clock IN
                        </a>
                    @else
                        <a href="{{ route('attendance.index') }}"
                            class="btn btn-full btn-sm rounded-sm bg-highlight font-700 text-uppercase mt-3">
                            <i class="fas fa-fingerprint"></i>
                            Clock OUT
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="content mb-3">
            <div class="row mb-0">
                <span href="#" class="color-theme font-800 font-13 text-uppercase px-3">
                    TODAY
                </span>
                <div class="col-6 pe-1">
                    <a href="#" data-menu="menu-transaction-transfer" class="card-style d-block bg-theme py-3 mx-0">
                        <span href="#" class="color-theme font-800 font-13 text-uppercase px-3">
                            <i class="fas fa-sign-in-alt color-green-dark pt-2 pe-2 float-end"></i>
                            CLOCK IN
                        </span>
                        <h4 class="font-700 text-uppercase font-20 mt-2 text-center">
                            @if (!$latestAttendance || $latestAttendance->clock_in == null)
                                --:--
                            @else
                                {{ $latestAttendance->clock_in->format('H:i') ?? '--:--' }}
                            @endif
                        </h4>
                    </a>
                </div>
                <div class="col-6 ps-1">
                    <a href="#" data-menu="menu-transaction-request" class="card-style d-block bg-theme py-3 mx-0">
                        <span href="#" class="color-theme font-800 font-13 text-uppercase px-3">
                            <i class="fas fa-sign-out-alt color-red-dark pt-2 pe-3 float-end"></i>CLOCK OUT
                        </span>
                        <h4 class="font-700 text-uppercase font-20 mt-2 text-center">
                            @if (!$latestAttendance || $latestAttendance->clock_out == null)
                                --:--
                            @else
                                {{ $latestAttendance->clock_out->format('H:i') ?? '--:--' }}
                            @endif
                        </h4>
                    </a>
                </div>
            </div>
            <div class="row text-center px-2">
                <div class="col-3">
                    <a href="{{ route('attendance.logs') }}"
                        class="icon icon-xxl bg-theme color-highlight shadow-l rounded-m"><i class="fas fa-history"></i></a>
                    <span class="font-10 font-500 color-theme d-block">Riwayat</span>
                </div>
                <div class="col-3">
                    <a href="{{ route('overtime.index') }}"
                        class="icon icon-xxl bg-theme color-highlight shadow-l rounded-m"><i
                            class="fas fa-user-clock"></i></a>
                    <span class="font-10 font-500 color-theme d-block">Lembur</span>
                </div>
                <div class="col-3">
                    <a href="{{ route('payslip') }}" class="icon icon-xxl bg-theme color-highlight shadow-l rounded-m"><i
                            class="fas fa-receipt"></i></a>
                    <span class="font-10 font-500 color-theme d-block">Payslip</span>
                </div>
                <div class="col-3">
                    <a href="{{ route('permit.index') }}"
                        class="icon icon-xxl bg-theme color-highlight shadow-l rounded-m"><i
                            class="fas fa-marker"></i></a>
                    <span class="font-10 font-500 color-theme d-block">Izin</span>
                </div>
            </div>

            {{-- <div class="row text-center px-2 pt-2">
                <div class="col-3">
                    <a href="{{ route('permit.index') }}"
                        class="icon icon-xxl bg-theme color-highlight shadow-l rounded-m"><i
                            class="fas fa-marker"></i></a>
                    <span class="font-10 font-500 color-theme d-block">Izin</span>
                </div>
                <div class="col-3">
                    <a href="{{ route('minute.index') }}"
                        class="icon icon-xxl bg-theme color-highlight shadow-l rounded-m"><i
                            class="fas fa-file-alt"></i></a>
                    <span class="font-10 font-500 color-theme d-block">Berita Acara</span>
                </div>
                <div class="col-3">
                    <a href="{{ route('leave.index') }}"
                        class="icon icon-xxl bg-theme color-highlight shadow-l rounded-m"><i
                            class="fas fa-suitcase-rolling"></i></a>
                    <span class="font-10 font-500 color-theme d-block">Cuti</span>
                </div>
                <div class="col-3">
                    <a href="#" data-menu="menu-transaction-transfer"
                        class="icon icon-xxl bg-theme color-highlight shadow-l rounded-m"><i
                            class="fas fa-plane-departure"></i></a>
                    <span class="font-10 font-500 color-theme d-block">Dinas</span>
                </div>
                <div class="col-3">
                    <a href="#" data-menu="menu-transaction-1"
                        class="icon icon-xxl bg-theme color-highlight shadow-l rounded-m"><i
                            class="fas fa-shapes"></i></a>
                    <span class="font-10 font-500 color-theme d-block">More</span>
                </div>
            </div> --}}
            <div class="row"> 
                <span href="#" class="color-theme font-800 font-13 text-uppercase px-3">
                    Supervisor Mode
                </span>
                <div class="mt-3">
                    <div class="list-group list-group-flush">
                        <a href="#"
                            class="list-group-item px-1 d-flex align-items-center p-3">
                            <div class="rounded-3 bg-blue-dark-supervisor size-icon-supervisor shadow-sm">
                                <i class="fa fa-users"></i>
                            </div>
    
                            <div class="ms-3">
                                <h4 class="mb-0">Kelola Tim</h4>
                                <p class="lh-1 text-secondary" style="font-size:11px;">Atur & monitor anggota tim</p>
                            </div>
    
                            <div class="ms-auto">
                                <i class="fa fa-angle-right"></i>
                            </div>
                        </a>
    
                        <a href="#"
                            class="list-group-item px-1 d-flex align-items-center p-3">
                            <div class="rounded-3 bg-blue-dark-supervisor size-icon-supervisor shadow-sm">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </div>
    
                            <div class="ms-3">
                                <h4 class="mb-0">Aproval Cuti</h4>
                                <p class="lh-1 text-secondary" style="font-size:11px;">Konfirmasi atau tolak pengajuan cuti
                                </p>
                            </div>
    
                            <div class="ms-auto">
                                <i class="fa fa-angle-right"></i>
                            </div>
                        </a>
    
                        <a href="#"
                            class="list-group-item px-1 d-flex align-items-center p-3">
                            <div class="rounded-3 bg-blue-dark-supervisor size-icon-supervisor shadow-sm">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </div>
    
                            <div class="ms-3">
                                <h4 class="mb-0">Aproval Ijin</h4>
                                <p class="lh-1 text-secondary" style="font-size:11px;">Konfirmasi atau tolak pengajuan ijin
                                </p>
                            </div>
    
                            <div class="ms-auto">
                                <i class="fa fa-angle-right"></i>
                            </div>
                        </a>
    
                        <a href="#"
                            class="list-group-item px-1 d-flex align-items-center p-3">
                            <div class="rounded-3 bg-blue-dark-supervisor size-icon-supervisor shadow-sm">
                                <i class="fa fa-clock" aria-hidden="true"></i>
                            </div>
    
                            <div class="ms-3">
                                <h4 class="mb-0">Aproval Lembur</h4>
                                <p class="lh-1 text-secondary" style="font-size:11px;">Review & approve jam lembur</p>
                            </div>
    
                            <div class="ms-auto">
                                <i class="fa fa-angle-right"></i>
                            </div>
                        </a>
    
                        <a href="#"
                            class="list-group-item px-1 d-flex align-items-center p-3">
                            <div class="rounded-3 bg-blue-dark-supervisor size-icon-supervisor shadow-sm">
                                <i class="fa fa-clock" aria-hidden="true"></i>
                            </div>
    
                            <div class="ms-3">
                                <h4 class="mb-0">Aproval Berita Acara</h4>
                                <p class="lh-1 text-secondary" style="font-size:11px;">Review & approve berita acara</p>
                            </div>
    
                            <div class="ms-auto">
                                <i class="fa fa-angle-right"></i>
                            </div>
                        </a>
    
                        <a href="#"
                            class="list-group-item px-1 d-flex align-items-center p-3">
                            <div class="rounded-3 bg-blue-dark-supervisor size-icon-supervisor shadow-sm">
                                <i class="fa fa-exchange" aria-hidden="true"></i>
                            </div>
    
                            <div class="ms-3">
                                <h4 class="mb-0">Tukar Jadwal Tim</h4>
                                <p class="lh-1 text-secondary" style="font-size:11px;">Atur & Setujui permintaan tukar jadwal
                                </p>
                            </div>
    
                            <div class="ms-auto">
                                <i class="fa fa-angle-right"></i>
                            </div>
                        </a>
    
                        <a href="#"
                            class="list-group-item px-1 d-flex align-items-center p-3">
                            <div class="rounded-3 bg-blue-dark-supervisor size-icon-supervisor shadow-sm">
                                <i class="fa fa-line-chart" aria-hidden="true"></i>
                            </div>
    
                            <div class="ms-3">
                                <h4 class="mb-0">Report Patroli</h4>
                                <p class="lh-1 text-secondary" style="font-size:11px;">Laporan performa & patroli</p>
                            </div>
    
                            <div class="ms-auto">
                                <i class="fa fa-angle-right"></i>
                            </div>
                        </a>
                        <li class="list-group-item"></li>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function getServerTime() {
            return $.ajax({
                async: false
            }).getResponseHeader('Date');
        }

        function realtimeClock() {
            var rtClock = new Date();

            var hours = rtClock.getHours();
            var minutes = rtClock.getMinutes();
            var seconds = rtClock.getSeconds();
            var day = rtClock.toLocaleDateString('id-ID', {
                weekday: 'long'
            });
            var date = rtClock.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            hours = ("0" + hours).slice(-2);
            minutes = ("0" + minutes).slice(-2);
            seconds = ("0" + seconds).slice(-2);

            document.getElementById("clock").innerHTML =
                hours + " : " + minutes + " : " + seconds;
            document.getElementById("date").innerHTML =
                day + ", " + date;

            var jamnya = setTimeout(realtimeClock, 500);
        }

        window.onload = function() {
            realtimeClock();
        };
    </script>
@endpush
