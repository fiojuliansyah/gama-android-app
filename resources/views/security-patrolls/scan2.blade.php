@extends('layouts.app')

@section('content')

<div class="header header-fixed header-logo-center">
    <span class="header-title">Security Patroll - Scan</span>
    <a href="{{ route('home') }}" class="header-icon header-icon-1">
        <i class="fas fa-arrow-left"></i>
    </a>
</div>

<div class="page-content pt-5">

    {{-- Notif --}}
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if(session('info'))
        <div class="alert alert-info text-center">{{ session('info') }}</div>
    @endif

    {{-- Jika belum ada sesi hari ini --}}
    @if(!$sessionToday)
        <div class="text-center mt-5">
            <h4 class="mb-2">Belum Memulai Patroli Hari Ini</h4>
            <p class="color-gray mb-4">Tekan tombol di bawah untuk memulai sesi patroli.</p>

            <form method="POST" action="{{ route('patroll.start') }}">
                @csrf
                <button class="btn btn-full btn-m bg-highlight rounded-s font-800">
                    <i class="fas fa-play"></i> &nbsp; Start Patroll
                </button>
            </form>
        </div>

    @else
        {{-- Kamera QR muncul jika sesi sudah ada --}}
        <div class="text-center mt-3">
            <h4 class="mb-2">Scan QR Lantai</h4>
            <p class="font-13 color-gray">Arahkan kamera ke QR Code untuk mulai patroli.</p>
        </div>

        <div id="reader" style="width: 100%; max-width: 400px; margin: auto; margin-top: 20px;"></div>

        {{-- QR Scanner --}}
        <script src="https://unpkg.com/html5-qrcode"></script>
        <script>
            function onScanSuccess(decodedText) {
                window.location.href = "/patroll/floor/" + decodedText;
            }

            const html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: 250 },
                false
            );

            html5QrcodeScanner.render(onScanSuccess);
        </script>

        {{-- TOMBOL END SESSION --}}
        @if($sessionToday && !$sessionToday->end_time)
            <div class="text-center mt-4 pb-5">
                <a href="{{ route('patroll.end-session', $sessionToday->id) }}"
                   class="btn btn-full btn-m bg-red-dark rounded-s font-800">
                    <i class="fas fa-stop-circle"></i> &nbsp; Akhiri Sesi Patroli
                </a>
            </div>
        @endif
    @endif

</div>

@endsection
