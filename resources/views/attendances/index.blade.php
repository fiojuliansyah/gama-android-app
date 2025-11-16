@extends('layouts.app')

@section('content')

<div class="page-content mb-0 pb-0">
    <div class="header-buttons" style="position: absolute; top: 20px; left: 0; right: 0; z-index: 1000; padding: 0 20px; display: flex; justify-content: space-between;">
        <!-- Back button -->
        <a href="{{ route('home') }}" class="btn btn-sm rounded-s bg-white color-black">
            <i class="fas fa-arrow-left"></i>
        </a>
        
        <a href="#" id="refreshMapBtn" class="btn btn-sm rounded-s bg-white color-black">
            <i class="fas fa-sync-alt"></i>
        </a>
    </div>
    <div class="card mb-0 map-full" data-card-height="cover">
        <div class="card-body" style="position: absolute; bottom: 100px; left: 50%; transform: translateX(-50%); z-index: 1000; background-color: #FFF; border-radius: 20px; width: 90%; max-width: 500px;">
            <div class="content">
                <div class="row">
                    <h6 class="page-title text-center pb-2">Detail Jadwal</h6>
                    <div class="col-4">
                        <strong style="color: black">Lokasi</strong>
                        <br>
                        <strong style="color: black">Waktu Kerja</strong>
                        <br>
                        <strong style="color: black">Status</strong>
                    </div>
                    <div class="col-8">
                        <small style="color: black">{{ Auth::user()->site['name'] }}</small>
                        <br>
                        @if ($schedule)
                            @if ($schedule->type == 'off')
                                <small style="color: black" class="text-center">LIBUR</small>
                            @else
                                <small style="color: black">{{ $schedule->clock_in ?? '' }} - {{ $schedule->clock_out ?? '' }}</small>
                            @endif
                        @else
                            <small style="color: black">No shift information available</small>
                        @endif   
                        <br>
                        @if ($latestAttendance && $latestAttendance->clock_out == Null)
                            <span class="badge bg-success">clock out</span>   
                        @else
                            <span class="badge bg-success">clock in</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div id="map" style="height: 100vh;"></div>
        
        @if ($latestClockIn)
            <a href="{{ route('attendance.clockout') }}" id="clockButton" class="btn btn-m bg-red-dark rounded-s text-uppercase font-900" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 1000; display: none;">
                <i class="fas fa-camera"></i>&nbsp; CLOCK OUT
            </a>
        @else
            <a href="{{ route('attendance.clockin') }}" id="clockButton" class="btn btn-m bg-red-dark rounded-s text-uppercase font-900" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 1000; display: none;">
                <i class="fas fa-camera"></i>&nbsp; CLOCK IN
            </a>
        @endif
    </div>

</div>

@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endpush

@push('js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var siteLat = {{ Auth::user()->site->lat ?? 0 }};
    var siteLong = {{ Auth::user()->site->long ?? 0 }};
    var radius = {{ Auth::user()->site->radius ?? 5 }};
    var clockButton = document.getElementById('clockButton');
    var userDepartment = {{ Auth::user()->department_id }};

    var map = L.map('map', {
        zoomControl: false
    }).setView([siteLat, siteLong], 18);

    // Gunakan OpenStreetMap tiles (lebih stabil)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Custom icon marker
    var userMarker = null;
    var siteCircle = null;
    var defaultIconUrl = 'https://img.icons8.com/?size=256&id=13783&format=png';
    var mobileIconUrl = 'https://img.icons8.com/?size=256&id=114446&format=png';

    // Panggil map.invalidateSize() supaya Leaflet tahu ukuran div
    setTimeout(function() { map.invalidateSize(); }, 100);

    function updateLocation(lat, lng) {
        if (userMarker) map.removeLayer(userMarker);
        if (siteCircle) map.removeLayer(siteCircle);

        var iconUrl = (userDepartment == 2) ? mobileIconUrl : defaultIconUrl;

        var customIcon = L.icon({
            iconUrl: iconUrl,
            iconSize: [48, 48],
            iconAnchor: [24, 48],
            popupAnchor: [0, -48]
        });

        userMarker = L.marker([lat, lng], { icon: customIcon })
            .addTo(map)
            .bindPopup(userDepartment == 2 ? "Status absensi bisa di mana saja" : "Pastikan anda dalam radius absen!")
            .openPopup();

        if (userDepartment != 2) {
            siteCircle = L.circle([siteLat, siteLong], {
                color: 'red',
                fillColor: 'red',
                fillOpacity: 0.2,
                radius: radius
            }).addTo(map);

            // Hitung jarak ke site
            var distance = map.distance([lat, lng], [siteLat, siteLong]);
            clockButton.style.display = (distance <= radius) ? 'block' : 'none';
        } else {
            clockButton.style.display = 'block';
        }

        map.setView([lat, lng], 18);
    }

    function onLocationFound(e) {
        updateLocation(e.latitude, e.longitude);
    }

    function onLocationError(e) {
        alert("Lokasi tidak dapat ditemukan: " + e.message);
        clockButton.style.display = 'none';
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            updateLocation(position.coords.latitude, position.coords.longitude);
        }, function(err) {
            console.warn(err);
            clockButton.style.display = 'none';
        });
    }

    // Tombol refresh map
    document.getElementById('refreshMapBtn').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                updateLocation(position.coords.latitude, position.coords.longitude);
            });
        }
    });
});
</script>

@endpush
