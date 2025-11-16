@extends('layouts.app')

@section('content')
<div class="page-content mb-0 pb-0">
    <!-- Header buttons -->
    <div class="header-buttons" style="position: absolute; top: 20px; left: 0; right: 0; z-index: 1000; padding: 0 20px; display: flex; justify-content: space-between;">
        <!-- Back button -->
        <a href="{{ route('home') }}" class="btn btn-sm rounded-s bg-white color-black">
            <i class="fas fa-arrow-left"></i>
        </a>
        
        <!-- Refresh location button -->
        <a href="#" id="refreshMapBtn" class="btn btn-sm rounded-s bg-white color-black">
            <i class="fas fa-sync-alt"></i>
        </a>
    </div>

    <!-- Clock and Date Display -->
    <div style="position: absolute; top: 60px; left: 0; right: 0; z-index: 999; text-align: center; color: white;">
        <h1 id="clock" style="margin: 0; font-size: 2rem;"></h1>
        <h6 id="date" style="margin: 0;"></h6>
    </div>

    <!-- Card untuk Content (Same as original) -->
    <div class="content" style="margin-top: 130px;">
        <a href="#" class="get-location btn btn-full btn-m bg-red-dark rounded-sm text-uppercase shadow-l font-900" hidden>Show My Location</a>
        <p class="location-coordinates" style="display: none"></p>
        <div id="map" style="height: 200px"></div>
        @if ($latestOvertime != null && $latestOvertime->clock_in !== null)
        <div class="row mb-0 pt-4">
            <div class="col-6">
                <strong>
                    <span style="display: block; text-align: center;">
                        MULAI
                    </span>
                    <span style="display: block; text-align: center;">
                        {{ \Carbon\Carbon::parse($latestOvertime->clock_in)->format('H:i') ?? '- - : - -' }}
                    </span>
                </strong>
            </div>
            <div class="col-6">
                @if ($clockOutStatus)
                    <strong>
                        <span style="display: block; text-align: center;">
                            SELESAI
                        </span>
                        <span style="display: block; text-align: center;">
                            {{ \Carbon\Carbon::parse($latestOvertime->clock_out)->format('H:i') ?? '- - : - -' }}
                        </span>
                    </strong>
                @else
                    <strong>
                        <span style="display: block; text-align: center;">
                            SELESAI
                        </span>
                        <span style="display: block; text-align: center;">
                            - - : - -
                        </span>
                    </strong>
                @endif
            </div>
            @if ($clockOutStatus)
            <div class="pt-4">
                @if ($latestOvertime->reason == null)
                    <div class="text-center">
                        <p>No Overtime information available</p>
                    </div>     
                @else
                    <div class="text-center">
                        <strong>{{ $latestOvertime->reason }}</strong>
                        <strong>{{ $latestOvertime->backup['name'] ?? '' }}</strong>
                    </div>  
                @endif
            </div>
            @else
                <div class="pt-4">
                    <form id="overtime-clockout" method="POST" action="{{ route('overtime.clockout') }}"
                        style="display: none;">
                        @csrf
                        <input type="hidden" name="latlong" id="latlongInput">
                    </form>
                    <a href="#"  onclick="event.preventDefault(); document.getElementById('overtime-clockout').submit();"
                        class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl btn-primary">
                        SELESAI
                    </a>
                </div>
            @endif
        </div>
        @else
        <div class="content" id="tab-group-1">
            <div class="tab-controls tabs-small tabs-rounded" data-highlight="bg-highlight">
                <a href="#" data-active data-bs-toggle="collapse" data-bs-target="#tab-1">
                    <span style="display: block; text-align: center;">
                        <i class="fas fa-clock">&nbsp</i> LEMBUR
                    </span>
                </a>
                <a href="#" data-bs-toggle="collapse" data-bs-target="#tab-2">
                    <span style="display: block; text-align: center;">
                        <i class="fa fa-rotate-right">&nbsp</i> BACKUP
                    </span>
                </a>
            </div>
            <div class="clearfix mb-3"></div>
            <div data-bs-parent="#tab-group-1" class="collapse show" id="tab-1">
                <form id="overtime-clockin-lembur" method="POST" action="{{ route('overtime.clockin') }}">
                    @csrf
                    <input type="hidden" name="latlong" id="latlongInput-lembur">
                    <input type="text" class="form-control" name="demand" placeholder="Request">
                    <br>
                    <input type="text" class="form-control" name="reason" placeholder="Tulis Tujuan Lembur Disini!!">
                    <div class="pt-4">
                        <button type="button" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl btn-primary clock-in-btn-lembur" style="width: 100%; padding: 20px; font-size: 18px;">
                            MULAI
                        </button>
                    </div>
                </form>
            </div>
            <div data-bs-parent="#tab-group-1" class="collapse" id="tab-2">
                <form id="overtime-clockin-backup" method="POST" action="{{ route('overtime.clockin') }}">
                    @csrf
                    <input type="hidden" name="latlong" id="latlongInput-backup">
                    <select class="form-control" name="backup_id">
                        <option value="default" disabled selected>Pilih yang akan di Backup</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <br>
                    <input type="date" name="change_date_from" id="" class="form-control">
                    <br>
                    <input type="text" class="form-control" name="demand" placeholder="Request">
                    <br>
                    <input type="text" class="form-control" name="reason" placeholder="Tulis Tujuan Lembur Disini!!">
                    <div class="pt-4">
                        <button type="button" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl btn-primary clock-in-btn-backup" style="width: 100%; padding: 20px; font-size: 18px;">
                            MULAI
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
    
    <div class="content mb-2">
        <h4 class="pb-4">Overtime Log</h4>
        <div class="clearfix"></div>
        <table class="table table-borderless text-center rounded-sm shadow-l" style="overflow: hidden;">
            <thead>
                <tr class="bg-blue1-dark">
                    <th scope="col" class="color-theme">Tanggal</th>
                    <th scope="col" class="color-theme">Waktu</th>
                    <th scope="col" class="color-theme">Validasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <th scope="row">{{ $log->attendance['date'] }}</th>
                        <td>
                            @if (isset($log->duration))
                                <span>{{ $log->duration }}</span>
                            @else
                                <span>Belum ada durasi</span>
                            @endif
                        </td>
                        <td class="color-green1-dark">Approve</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="loader" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 30;">
        <div class="spinner" style="border: 8px solid #f3f3f3; border-top: 8px solid #00B5CC; border-radius: 50%; width: 60px; height: 60px; animation: spin 1s linear infinite;"></div>
    </div>
</div>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    #map {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .leaflet-control-zoom {
        display: none !important;
    }
</style>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endpush

@push('js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    function getServerTime() {
        return $.ajax({ async: false }).getResponseHeader('Date');
    }

    function realtimeClock() {
        var rtClock = new Date();

        var hours = rtClock.getHours();
        var minutes = rtClock.getMinutes();
        var seconds = rtClock.getSeconds();
        var day = rtClock.toLocaleDateString('id-ID', { weekday: 'long' });
        var date = rtClock.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });

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

    document.addEventListener("DOMContentLoaded", function () {
        var map = null;
        var userMarker = null;
        var radiusCircle = null;
        var userLat = @json(Auth::user()->site['lat']);
        var userLong = @json(Auth::user()->site['long']);
        var radius = @json(Auth::user()->site['radius']);
        var mobileDepartment = @json(Auth::user()->department_id);
        
        function geoLocate() {
            const locationCoordinates = document.querySelector('.location-coordinates');
            const clockInButtonsLembur = document.querySelectorAll('.clock-in-btn-lembur');
            const clockInButtonsBackup = document.querySelectorAll('.clock-in-btn-backup');
            const finishButtons = document.querySelectorAll('.btn-primary');

            function success(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                locationCoordinates.innerHTML = '<strong>Longitude:</strong> ' + longitude + '<br><strong>Latitude:</strong> ' + latitude;

                if (!map) {
                    map = L.map('map', {
                        zoomControl: false
                    }).setView([latitude, longitude], 16);

                    L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    }).addTo(map);
                } else {
                    map.setView([latitude, longitude], 16);
                    
                    if (userMarker) {
                        map.removeLayer(userMarker);
                    }
                    
                    if (radiusCircle) {
                        map.removeLayer(radiusCircle);
                    }
                }

                if (mobileDepartment == '2') {
                    const customIcon = L.icon({
                        iconUrl: 'https://img.icons8.com/?size=256&id=114446&format=png',
                        iconSize: [48],
                        iconAnchor: [24, 48],
                        popupAnchor: [0, -48]
                    });

                    userMarker = L.marker([latitude, longitude], { icon: customIcon }).addTo(map)
                        .bindPopup('Status absensi anda bisa dimana saja!')
                        .openPopup();

                } else {
                    const customIcon = L.icon({
                        iconUrl: 'https://img.icons8.com/?size=256&id=13783&format=png',
                        iconSize: [48],
                        iconAnchor: [24, 48],
                        popupAnchor: [0, -48]
                    });

                    userMarker = L.marker([latitude, longitude], { icon: customIcon }).addTo(map)
                        .bindPopup('Pastikan anda dalam radius absen!')
                        .openPopup();

                    radiusCircle = L.circle([userLat, userLong], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: radius
                    }).addTo(map);
                }

                const distance = haversineDistance(latitude, longitude, userLat, userLong) * 1000;

                // Update lembur clock-in buttons
                clockInButtonsLembur.forEach(button => {
                    if (mobileDepartment == '2' || distance <= radius) {
                        button.classList.remove('btn-secondary');
                        button.classList.add('btn-primary');
                        button.style.pointerEvents = 'auto';
                    } else {
                        button.classList.remove('btn-primary');
                        button.classList.add('btn-secondary');
                        button.style.pointerEvents = 'none';
                    }
                });

                // Update backup clock-in buttons
                clockInButtonsBackup.forEach(button => {
                    if (mobileDepartment == '2' || distance <= radius) {
                        button.classList.remove('btn-secondary');
                        button.classList.add('btn-primary');
                        button.style.pointerEvents = 'auto';
                    } else {
                        button.classList.remove('btn-primary');
                        button.classList.add('btn-secondary');
                        button.style.pointerEvents = 'none';
                    }
                });

                // Update finish buttons
                finishButtons.forEach(button => {
                    if (mobileDepartment == '2' || distance <= radius) {
                        button.classList.remove('btn-secondary');
                        button.classList.add('btn-primary');
                        button.style.pointerEvents = 'auto';
                    } else {
                        button.classList.remove('btn-primary');
                        button.classList.add('btn-secondary');
                        button.style.pointerEvents = 'none';
                    }
                });

                document.querySelector('.get-location').setAttribute('href', `https://www.google.com/maps?q=${latitude},${longitude}&z=16`);
                
                // Update hidden input fields with coordinates
                const latlongValue = `${latitude},${longitude}`;
                if (document.getElementById('latlongInput')) {
                    document.getElementById('latlongInput').value = latlongValue;
                }
                if (document.getElementById('latlongInput-lembur')) {
                    document.getElementById('latlongInput-lembur').value = latlongValue;
                }
                if (document.getElementById('latlongInput-backup')) {
                    document.getElementById('latlongInput-backup').value = latlongValue;
                }
            }

            function error() {
                locationCoordinates.textContent = 'Unable to retrieve your location';
            }

            if (!navigator.geolocation) {
                locationCoordinates.textContent = 'Geolocation is not supported by your browser';
            } else {
                locationCoordinates.textContent = 'Locating…';
                navigator.geolocation.getCurrentPosition(success, error);
            }
        }

        function haversineDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the Earth in km
            const dLat = (lat2 - lat1) * (Math.PI / 180);
            const dLon = (lon2 - lon1) * (Math.PI / 180);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // Initialize map on page load
        geoLocate();
        
        // Add refresh button functionality
        document.getElementById('refreshMapBtn').addEventListener('click', function() {
            geoLocate();
        });
        
        // Setup clock-in buttons
        const clockInButtonsLembur = document.querySelectorAll('.clock-in-btn-lembur');
        const clockInButtonsBackup = document.querySelectorAll('.clock-in-btn-backup');

        function getLocationAndSubmit(formSuffix) {
            if (navigator.geolocation) {
                document.getElementById('loader').style.display = 'block';
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latlong = `${position.coords.latitude},${position.coords.longitude}`;
                    document.getElementById(`latlongInput-${formSuffix}`).value = latlong;
                    document.getElementById(`overtime-clockin-${formSuffix}`).submit();
                }, function(error) {
                    document.getElementById('loader').style.display = 'none';
                    console.error('Error getting location:', error);
                    alert('Could not get your location. Please enable location services.');
                });
            } else {
                console.error('Geolocation is not supported by this browser.');
                alert('Geolocation is not supported by your browser.');
            }
        }

        clockInButtonsLembur.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                getLocationAndSubmit('lembur');
            });
        });

        clockInButtonsBackup.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                getLocationAndSubmit('backup');
            });
        });
    });
</script>
@endpush