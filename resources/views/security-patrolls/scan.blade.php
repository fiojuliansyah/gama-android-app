@extends('layouts.app')

@section('content')

<a href="#" class="btn-back">
    <i class="fas fa-arrow-left"></i>
</a>

<div id="reader"></div>

<div class="qr-overlay">
    <div class="qr-header">
        Scan QR Code
    </div>

    <div class="qr-footer">
        <div class="qr-result mb-5">
            Hasil: <span id="result">Pastikan anda online</span>
            <input type="text" id="result-input" hidden>
        </div>

        <div class="mt-3 d-flex gap-3 justify-content-center">
            @if (!$sessionToday)
                <form method="POST" action="{{ route('patroll.start') }}">
                    @csrf
                    <button class="btn btn-full btn-m bg-highlight rounded-s font-800">
                        <i class="fas fa-play"></i> &nbsp; Start Patroll
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection

@if ($sessionToday && !$sessionToday->end_time)
<div class="end-session-wrapper">
    <a href="{{ route('patroll.end-session', $sessionToday->id) }}"
        class="btn btn-full btn-m bg-red-dark rounded-s font-800">
        <i class="fas fa-stop-circle"></i> &nbsp; Akhiri Sesi Patroli
    </a>
</div>
@endif

@push('css')
<style>
    #reader { position: fixed; inset: 0; width: 100%; height: 100%; z-index: 10; }
    #reader video { height: 100vh; pointer-events: none !important; }
    .qr-overlay {
        position: fixed; inset: 0; z-index: 20; padding: 20px;
        display: flex; flex-direction: column; justify-content: space-between;
        background: linear-gradient(to bottom,
            rgba(0,0,0,0.5) 0%,
            rgba(0,0,0,0.2) 20%,
            rgba(0,0,0,0.1) 60%,
            rgba(0,0,0,0.3) 100%
        );
    }
    .qr-header { text-align: center; color: #fff; font-size: 20px; font-weight: bold; }
    .qr-footer { text-align: center; color: #fff; font-size: 16px; margin-bottom: 20px; }
    .qr-result {
        width: 300px; display: inline-block; padding: 10px 15px; font-size: 13px;
        border-radius: 8px; background-color: rgba(0,0,0,0.6);
    }
    .btn-back {
        position: absolute; top: 15px; left: 15px; width: 40px; height: 40px;
        z-index: 30; display: flex; justify-content: center; align-items: center;
        color: #fff; border-radius: 50%; text-decoration: none;
        background: rgba(0,0,0,0.5);
    }
    .end-session-wrapper {
        position: fixed; bottom: 30px; left: 0; right: 0;
        z-index: 9999999; display: flex; justify-content: center;
    }
</style>
@endpush

@push('js')
@if ($sessionToday && !$sessionToday->end_time)
<script src="https://unpkg.com/html5-qrcode"></script>

<audio id="beep-sound">
    <source src="https://actions.google.com/sounds/v1/cartoon/clang_and_wobble.ogg" type="audio/ogg">
</audio>

<script>
    let scanner;
    let beep = document.getElementById("beep-sound");
    let scanned = false; // mencegah double redirect

    document.addEventListener("DOMContentLoaded", async function () {
        scanner = new Html5Qrcode("reader");

        try {
            const cameras = await Html5Qrcode.getCameras();
            if (!cameras.length) {
                document.getElementById("result").innerText = "Tidak ada kamera ditemukan";
                return;
            }

            let cam = cameras.find(c =>
                c.label.toLowerCase().includes("back") ||
                c.label.toLowerCase().includes("environment")
            ) || cameras[0];

            await scanner.start(
                { deviceId: cam.id },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                text => {
                    if (scanned) return;
                    scanned = true;

                    beep.play();

                    document.getElementById("result").innerText = text;
                    document.getElementById("result-input").value = text;

                    setTimeout(() => {
                        window.location.href = text;
                    }, 300); // sedikit delay setelah beep
                }
            );

        } catch (e) {
            document.getElementById("result").innerText = "Gagal membuka kamera";
        }
    });
</script>
@endif
@endpush
