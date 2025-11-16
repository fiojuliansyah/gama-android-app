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

        L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }).addTo(map);

        var customIcon = L.icon({
            iconUrl: 'https://img.icons8.com/?size=256&id=13783&format=png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        function onLocationFound(e) {
            var userLat = e.latlng.lat;
            var userLong = e.latlng.lng;
            
            L.marker([userLat, userLong], { icon: customIcon }).addTo(map)
                .bindPopup("<b>Lokasi Anda</b>").openPopup();
            
            if (userDepartment == 2) {
                clockButton.style.display = 'block';
            } else {
                var distance = map.distance([userLat, userLong], [siteLat, siteLong]);
                if (distance <= radius) {
                    clockButton.style.display = 'block';
                    L.circle([siteLat, siteLong], {
                        color: 'red',
                        fillColor: 'red',
                        fillOpacity: 0.2,
                        radius: radius
                    }).addTo(map);
                } else {
                    clockButton.style.display = 'none';
                }
            }
        }

        function onLocationError(e) {
            alert("Lokasi tidak dapat ditemukan: " + e.message);
        }

        map.locate({ setView: true, maxZoom: 18, watch: true });
        map.on('locationfound', onLocationFound);
        map.on('locationerror', onLocationError);

        document.getElementById('refreshMapBtn').addEventListener('click', function() {
            map.eachLayer(function(layer) {
                if (layer instanceof L.Marker || layer instanceof L.Circle) {
                    map.removeLayer(layer);
                }
            });
            
            map.locate({ setView: true, maxZoom: 18 });
        });
    });
</script>
@endpush
