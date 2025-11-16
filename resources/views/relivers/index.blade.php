@extends('layouts.module')


@section('content')
<div class="page-content">  
    <div class="page-title page-title-small" style="margin-top: 50px">
        <a href="{{ route('home') }}" class="btn rounded-xl" style="background-color: #1CB3CA">
            <span style="display: block; text-align: center;">
                <i class="fa fa-arrow-left" style="font-size: 12px">&nbsp</i>
            </span>
        </a>
    </div>
    <div class="card header-card">
        <a href="#" class="get-location btn btn-full btn-m bg-red2-dark rounded-sm text-uppercase shadow-l font-900" style="display: none">Show My Location</a>
        <p class="location-coordinates" style="display: none"></p>
        <div id="map" style="height: 400px"></div>
    </div>
    <div class="content" style="margin-top: 360px">
        <div class="row">
            <div class="col-2 pt-2">
                <div class="btn rounded-xl bg-green-dark">
                    <span style="display: block; text-align: center;">
                        <i class="fa fa-sign-in" style="font-size: 12px">&nbsp</i>
                    </span>
                </div>
            </div>
            <div class="col-8">
                <strong id="date" style="color: black"></strong>
                <br>
                <strong id="clock"></strong>
            </div>
            <div class="col-2 pt-2">
                <a href="#" onclick="refreshMap()" class="btn rounded-xl bg-yellow-dark">
                    <span style="display: block; text-align: center;">
                        <i class="fas fa-map-marker-alt" style="font-size: 12px"></i>
                    </span>
                </a>
            </div>
        </div>
            @if ($clockInStatus)
                
            @else
            <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
                <select name="site_id" class="form-select">
                    <option value="default" disabled selected>pilih</option>
                    @foreach ($sites as $site)    
                        <option value="{{ $site->id }}"  {{ $user->site_id == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                    @endforeach
                </select>
                <label for="form2" class="color-highlight font-400 font-13">Pilih Lokasi</label>
                <em><i class="fa fa-angle-down"></i></em>
            </div>
            @endif
        <div style="border: 1px solid #eef2f1; border-radius: 8px; padding: 10px; box-sizing: border-box;">
            <h5 class="font-16 font-500">Mode Reliver</h5>
            @if ($clockInStatus)
                
            @else
            <form class="form" action="{{ route('reliver.clockin') }}" method="POST" id="reliver-form">
                @csrf
                <div class="mt-3">
                    <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
                        <select name="site_id" class="form-select select2">
                            <option value="default" disabled selected>pilih</option>
                            @foreach ($sites as $site)    
                                <option value="{{ $site->id }}"  {{ $user->site_id == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                            @endforeach
                        </select>
                        <label for="form2" class="color-highlight font-400 font-13">Pilih Lokasi</label>
                        <em><i class="fa fa-angle-down"></i></em>
                    </div>
                </div>
                <div class="mt-2" id="backupUserSelect" style="display: none;">
                    <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
                        <select name="backup_id" class="form-select">
                            <option value="default" disabled selected>pilih</option>
                            @foreach ($users as $user)    
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <label for="form2" class="color-highlight font-400 font-13">Pilih Employee</label>
                        <em><i class="fa fa-angle-down"></i></em>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
                        <textarea name="remark" class="form-control"></textarea>
                        <label for="form2" class="color-highlight font-400 font-13">Alasan</label>
                    </div>
                </div>
            </form>
            @endif
            <div class="row">
                <div class="col-4">
                    <strong style="color: black">Judul Shift</strong>
                    <br>
                    <strong style="color: black">Lokasi Terakhir</strong>
                    <br>
                    <strong style="color: black">Waktu Kerja</strong>
                    <br>
                    <strong style="color: black">Status</strong>
                </div>
                <div class="col-8">
                    <small style="color: black">No shift information available</small>
                    <br>
                    <small style="color: black">{{ Auth::user()->site['name'] }}</small>
                    <br>
                    <small style="color: black">No shift information available</small>
                    <br>
                    @if ($clockInStatus)
                        <span class="badge bg-success">clock out</span>   
                    @else
                        <span class="badge bg-success">clock in</span>
                    @endif
                </div>
            </div>
        </div>        
        <div class="mb-2 pt-1 mt-2">
            <h5 class="font-16 font-500">Absen Terakhir</h5>
            <div class="clearfix"></div>
            <table class="table table-borderless text-center rounded-sm" style="overflow: hidden;">
                <thead>
                    <tr class="bg-blue1-dark">
                        <th scope="col" class="color-theme">Tanggal</th>
                        <th scope="col" class="color-theme">Clock In</th>
                        <th scope="col" class="color-theme">Clock Out</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)     
                    <tr>
                        <th scope="row">{{ $log->date->format('d-M-Y') ?? '' }}</th>
                        <td><a href="#" data-menu="menu-maps-in" class="color-green1-dark">{{ $log->clock_in ?? '' }}</a></td>
                        <td><a href="#" data-menu="menu-maps-out" class="color-red1-dark">{{ $log->clock_out ?? '' }}</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="ad-300x50 ad-300x50-fixed">
        <div class="content">
            @if ($clockOutStatus)
                
            @else
                @if ($clockInStatus)
                    <a href="{{ route('reliver.clockout') }}" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight clock-out-btn">
                        <i class="fas fa-camera">&nbsp;</i>Pulang
                    </a>
                @else
                    <a href="#" onclick="event.preventDefault(); document.getElementById('reliver-form').submit();" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight clock-in-btn">
                        <i class="fas fa-camera">&nbsp;</i>Masuk
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>
<div id="loader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.5); z-index: 1000;">
    <div class="spinner" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); border: 8px solid #f3f3f3; border-top: 8px solid #00B5CC; border-radius: 50%; width: 60px; height: 60px; animation: spin 1s linear infinite;"></div>
</div>
<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@push('js')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Apply Select2 to your select element
            $('.select2').select2({
                width: '100%', // Optional, to ensure full width
                placeholder: "Pilih Lokasi", // Placeholder text
                allowClear: true // Optional, to allow clearing the selection
            });
        });
    </script>
    <script>
        document.querySelector('select[name="site_id"]').addEventListener('change', function() {
            var siteId = this.value;

            // Show the loader
            document.getElementById('loader').style.display = 'block';

            $.ajax({
                url: '{{ route("reliver.updateSite") }}',
                type: 'POST',
                data: {
                    site_id: siteId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Hide the loader
                    document.getElementById('loader').style.display = 'none';

                    if(response.success) {
                        location.reload();
                    } else {
                        alert('Failed to update site.');
                    }
                },
                error: function() {
                    // Hide the loader
                    document.getElementById('loader').style.display = 'none';

                    alert('An error occurred. Please try again.');
                }
            });
        });
    </script>    
    <script>
        document.getElementById('reliverTypeSelect').addEventListener('change', function() {
            var backupUserSelect = document.getElementById('backupUserSelect');
            if (this.value === 'backup') {
                backupUserSelect.style.display = 'block';
            } else {
                backupUserSelect.style.display = 'none';
            }
        });
    </script>
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
    </script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map;
        var userLat = @json(Auth::user()->site['lat']);
        var userLong = @json(Auth::user()->site['long']);
        var radius = @json(Auth::user()->site['radius']);
        var mobileDepartment = @json(Auth::user()->department_id);
    
        function geoLocate() {
            const locationCoordinates = document.querySelector('.location-coordinates');
            const clockInButton = document.querySelectorAll('.clock-in-btn');
            const clockOutButton = document.querySelectorAll('.clock-out-btn');
            const disableButton = document.querySelectorAll('.bg-highlight');
    
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
    
                    // Add initial marker and circle
                    addMapMarkersAndCircles(latitude, longitude);
    
                } else {
                    // Update map view without recreating the map
                    map.setView([latitude, longitude], 16);
                    updateMapMarkersAndCircles(latitude, longitude);
                }
    
                const distance = haversineDistance(latitude, longitude, userLat, userLong) * 1000;
    
                updateButtonStates(distance);
                document.querySelector('.get-location').setAttribute('href', `https://www.google.com/maps?q=${latitude},${longitude}&z=16`);
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
    
        function addMapMarkersAndCircles(lat, lon) {
            if (mobileDepartment == '2') {
                const customIcon = L.icon({
                    iconUrl: 'https://img.icons8.com/?size=256&id=114446&format=png',
                    iconSize: [48],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });
                L.marker([lat, lon], { icon: customIcon }).addTo(map)
                    .bindPopup('Status absensi anda bisa dimana saja!')
                    .openPopup();
            } else {
                const customIcon = L.icon({
                    iconUrl: 'https://img.icons8.com/?size=256&id=13783&format=png',
                    iconSize: [48],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });
                L.marker([lat, lon], { icon: customIcon }).addTo(map)
                    .bindPopup('Pastikan anda dalam radius absen!')
                    .openPopup();
    
                L.circle([userLat, userLong], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: radius
                }).addTo(map);
            }
        }
    
        function updateMapMarkersAndCircles(lat, lon) {
            map.eachLayer(function (layer) {
                if (layer instanceof L.Marker || layer instanceof L.Circle) {
                    map.removeLayer(layer);
                }
            });
            addMapMarkersAndCircles(lat, lon);
        }
    
        function updateButtonStates(distance) {
            const clockInButton = document.querySelectorAll('.clock-in-btn');
            const clockOutButton = document.querySelectorAll('.clock-out-btn');
            const disableButton = document.querySelectorAll('.bg-highlight');
    
            clockInButton.forEach(button => {
                if (mobileDepartment == '2' || distance <= radius) {
                    button.classList.remove('btn-secondary');
                    button.classList.add('bg-highlight');
                    button.style.pointerEvents = 'auto';
                } else {
                    button.classList.remove('bg-highlight');
                    button.classList.add('btn-secondary');
                    button.style.pointerEvents = 'none';
                }
            });
    
            clockOutButton.forEach(button => {
                if (mobileDepartment == '2' || distance <= radius) {
                    button.classList.remove('btn-secondary');
                    button.classList.add('bg-highlight');
                    button.style.pointerEvents = 'auto';
                } else {
                    button.classList.remove('bg-highlight');
                    button.classList.add('btn-secondary');
                    button.style.pointerEvents = 'none';
                }
            });
    
            disableButton.forEach(button => {
                if (mobileDepartment == '2' || distance <= radius) {
                    button.classList.remove('btn-secondary');
                    button.classList.add('bg-highlight');
                    button.style.pointerEvents = 'auto';
                } else {
                    button.classList.remove('bg-highlight');
                    button.classList.add('btn-secondary');
                    button.style.pointerEvents = 'none';
                }
            });
        }
    
        function haversineDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * (Math.PI / 180);
            const dLon = (lon2 - lon1) * (Math.PI / 180);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }
    
        document.addEventListener('DOMContentLoaded', function() {
            geoLocate();
        });
    
        function refreshMap() {
            geoLocate();
        }
    </script>
@endpush