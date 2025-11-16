@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <a href="{{ route('home') }}" class="header-title">Riwayat Absensi</a>
    <a href="{{ route('home') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
    <a href="#" class="header-icon header-icon-4" onclick="showFilterModal()"><i class="fas fa-filter"></i></a>
</div>

<div class="page-content pt-5">
    <!-- Date Filter Info -->
    @if(request('start_date') || request('end_date'))
    <div class="content mb-0 mt-2">
        <div class="date-filter-badge">
            <i class="far fa-calendar-alt"></i>
            Filter: 
            {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'All' }}
            - 
            {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'Now' }}
            <a href="{{ route('attendance.logs') }}" class="reset-filter">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </div>
    @endif
    
    <!-- Attendance Cards -->
    @if(count($logs) > 0)
        @foreach ($logs as $index => $log)
        <div class="card card-style attendance-card">
            <div class="content">
                <!-- Date Badge -->
                <div class="date-badge">
                    <span class="date-number">{{ $log->date->format('d') }}</span>
                    <span class="date-month">{{ $log->date->format('M') }}</span>
                </div>
                
                <!-- Log Details -->
                <div class="log-details">
                    <h4>{{ $log->site['name'] }}</h4>
                    
                    @if ($log->type == 'shift_off')
                        <span class="badge bg-off">LIBUR</span>
                    @else
                        <div class="location-info" id="address-{{ $index }}">
                            <i class="fa fa-map-marker-alt"></i>
                            <span class="loading-text">Memuat lokasi...</span>
                        </div>
                        
                        <!-- Time Info -->
                        <div class="time-info">
                            @if($log->clock_in)
                                <div class="time-item">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>{{ $log->clock_in->format('H:i') }}</span>
                                </div>
                            @endif
                            
                            @if($log->clock_out)
                                <div class="time-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>{{ $log->clock_out->format('H:i') }}</span>
                                </div>
                                
                                <!-- Duration -->
                                @if($log->clock_in)
                                    <div class="time-item duration">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ \Carbon\Carbon::parse($log->clock_in)->diff(\Carbon\Carbon::parse($log->clock_out))->format('%H jam %I menit') }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        <!-- View Map Button -->
                        <button onclick="viewOnMap({{ $index }})" class="btn btn-s rounded-s text-uppercase font-900 shadow-s bg-highlight">
                            <i class="fas fa-map-marked-alt mr-2"></i> Lihat Peta
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        
        <!-- Pagination Links -->
        <div class="content mb-3">
            <div class="pagination-container">
                {{ $logs->links() }}
            </div>
        </div>
    @else
        <div class="card card-style">
            <div class="content mb-0">
                <div class="empty-state">
                    <img src="{{ asset('images/empty-logs.svg') }}" alt="No Logs" class="empty-image">
                    <h3>Tidak Ada Riwayat</h3>
                    <p>Belum ada riwayat absensi yang tercatat</p>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Map Modal -->
<div id="mapModal" class="modal-overlay">
    <div class="modal-container map-modal-container">
        <div class="modal-header">
            <h3 id="mapTitle">Detail Lokasi</h3>
            <a href="#" class="close-modal" onclick="closeMap()">
                <i class="fas fa-times"></i>
            </a>
        </div>
        <div id="map" class="map-view"></div>
        <div class="map-footer">
            <div id="mapAddress" class="map-address">
                <i class="fas fa-location-arrow"></i>
                <span>Loading address...</span>
            </div>
            <div class="map-actions">
                <button class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-s bg-highlight" 
                        id="openInGoogleMaps">
                    Buka di Google Maps
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="modal-overlay">
    <div class="modal-container filter-modal-container">
        <div class="modal-header">
            <h3>Filter Riwayat</h3>
            <a href="#" class="close-modal" onclick="closeFilterModal()">
                <i class="fas fa-times"></i>
            </a>
        </div>
        <div class="modal-content">
            <form method="GET" action="{{ route('attendance.logs') }}">
                <div class="form-group">
                    <label class="color-highlight">Tanggal Mulai</label>
                    <div class="input-style input-style-always-active has-borders no-icon validate-field mb-4">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="color-highlight">Tanggal Akhir</label>
                    <div class="input-style input-style-always-active has-borders no-icon validate-field mb-4">
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        <i class="fa fa-times disabled invalid color-red-dark"></i>
                        <i class="fa fa-check disabled valid color-green-dark"></i>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-s bg-highlight">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    /* Card Styling */
    .attendance-card {
        margin: 0.7rem 16px;
        padding: 0;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .attendance-card:active {
        transform: scale(0.98);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .attendance-card .content {
        display: flex;
        padding: 15px;
    }
    
    /* Date Badge */
    .date-badge {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: rgba(var(--gradient-highlight-1), 0.15);
        border-radius: 8px;
        min-width: 60px;
        height: 60px;
        margin-right: 15px;
    }
    
    .date-number {
        font-size: 22px;
        font-weight: 700;
        line-height: 1;
        color: var(--color-highlight);
    }
    
    .date-month {
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        margin-top: 2px;
        color: var(--color-highlight);
    }
    
    /* Log Details */
    .log-details {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .log-details h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: bold;
    }
    
    .location-info {
        display: flex;
        align-items: center;
        font-size: 12px;
        color: #666;
        margin-bottom: 8px;
        min-height: 20px;
    }
    
    .location-info i {
        color: var(--color-highlight);
        margin-right: 5px;
        font-size: 14px;
    }
    
    .loading-text {
        font-style: italic;
        opacity: 0.7;
    }
    
    /* Badge */
    .badge.bg-off {
        background-color: #dc3545;
        color: white;
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 30px;
        display: inline-block;
        margin-bottom: 5px;
    }
    
    /* Time Information */
    .time-info {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    
    .time-item {
        display: flex;
        align-items: center;
        margin-right: 15px;
        font-size: 13px;
        color: #444;
    }
    
    .time-item i {
        margin-right: 5px;
        color: var(--color-highlight);
    }
    
    .time-item.duration {
        font-weight: 600;
    }
    
    /* Map Button */
    .log-details button {
        align-self: flex-start;
        padding: 8px 16px;
        font-size: 12px;
        transition: transform 0.2s;
    }
    
    .log-details button:active {
        transform: scale(0.95);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 30px;
    }
    
    .empty-image {
        width: 120px;
        height: auto;
        margin-bottom: 20px;
        opacity: 0.8;
    }
    
    .empty-state h3 {
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #666;
    }
    
    /* Filter Badge */
    .date-filter-badge {
        background-color: rgba(var(--gradient-highlight-1), 0.15);
        color: var(--color-highlight);
        padding: 8px 12px;
        border-radius: 30px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
    }
    
    .date-filter-badge i {
        margin-right: 5px;
    }
    
    .reset-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        background: var(--color-highlight);
        border-radius: 50%;
        margin-left: 8px;
    }
    
    .reset-filter i {
        color: white;
        font-size: 8px;
        margin: 0;
    }
    
    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }
    
    .pagination-container .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .pagination-container .page-item {
        margin: 0 2px;
    }
    
    .pagination-container .page-link {
        padding: 5px 10px;
        border-radius: 5px;
        background: #f5f5f5;
        color: #333;
        text-decoration: none;
        font-size: 14px;
    }
    
    .pagination-container .page-item.active .page-link {
        background: var(--color-highlight);
        color: white;
    }
    
    /* Modal Styling */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 99999;
        animation: fadeIn 0.3s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .modal-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        animation: scaleIn 0.3s;
    }
    
    @keyframes scaleIn {
        from { transform: translate(-50%, -50%) scale(0.9); opacity: 0; }
        to { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    }
    
    .map-modal-container {
        width: 90%;
        height: 80%;
        max-width: 500px;
        display: flex;
        flex-direction: column;
    }
    
    .filter-modal-container {
        width: 90%;
        max-width: 400px;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }
    
    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }
    
    .close-modal {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #f1f1f1;
        color: #333;
        text-decoration: none !important;
        transition: background-color 0.2s;
    }
    
    .close-modal:hover {
        background-color: #e0e0e0;
    }
    
    .map-view {
        flex: 1;
        width: 100%;
        z-index: 1;
    }
    
    .map-footer {
        padding: 15px;
        border-top: 1px solid #eee;
    }
    
    .map-address {
        font-size: 14px;
        margin-bottom: 15px;
        line-height: 1.4;
        color: #444;
    }
    
    .map-address i {
        color: var(--color-highlight);
        margin-right: 5px;
    }
    
    .modal-content {
        padding: 20px;
    }
    
    /* Custom Marker */
    .custom-marker {
        background-color: var(--color-highlight);
        border: 2px solid white;
        border-radius: 50%;
        width: 20px !important;
        height: 20px !important;
        display: block;
        position: relative;
        box-shadow: 0 0 8px rgba(0,0,0,0.3);
    }
    
    .custom-marker::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-top: 10px solid var(--color-highlight);
    }
</style>
@endpush

@push('js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Initialize variables
    let map = null;
    let currentMarker = null;
    let geocodedAddresses = {};

    // Initialize map when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Start fetching addresses for all logs
        fetchAllAddresses();
    });

    // Fetch all addresses for all logs
    function fetchAllAddresses() {
        @foreach ($logs as $index => $log)
            // Skip if it's a shift_off type
            @if ($log->type != 'shift_off' && $log->latlong)
                (function(index) {
                    let latlng = "{{ $log->latlong }}".split(',');
                    if (latlng.length === 2) {
                        let lat = parseFloat(latlng[0]);
                        let lng = parseFloat(latlng[1]);
                        
                        if (!isNaN(lat) && !isNaN(lng)) {
                            // Use reverse geocoding
                            fetchAddress(lat, lng, index);
                        } else {
                            updateAddressElement(index, 'Lokasi tidak valid');
                        }
                    } else {
                        updateAddressElement(index, 'Lokasi tidak tersedia');
                    }
                })({{ $index }});
            @else
                updateAddressElement({{ $index }}, 'Libur');
            @endif
        @endforeach
    }

    // Fetch address using reverse geocoding
    function fetchAddress(lat, lng, index) {
        const cacheKey = `${lat},${lng}`;
        
        // Check if we already have this address cached
        if (geocodedAddresses[cacheKey]) {
            updateAddressElement(index, geocodedAddresses[cacheKey]);
            return;
        }
        
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                let address = data.display_name || 'Alamat tidak tersedia';
                // Cache the address
                geocodedAddresses[cacheKey] = address;
                updateAddressElement(index, address);
            })
            .catch(error => {
                console.error('Error fetching address:', error);
                updateAddressElement(index, 'Alamat tidak tersedia');
            });
    }

    // Update address element with fetched address
    function updateAddressElement(index, address) {
        const addressElement = document.getElementById(`address-${index}`);
        if (addressElement) {
            addressElement.innerHTML = `<i class="fa fa-map-marker-alt"></i><span>${address}</span>`;
        }
    }

    // View location on map
    function viewOnMap(index) {
        // Show the map modal
        document.getElementById('mapModal').style.display = 'flex';
        document.documentElement.style.overflow = 'hidden'; // Prevent body scrolling
        
        // Get log data
        @foreach ($logs as $logIndex => $log)
            if ({{ $logIndex }} === index) {
                // Update the map title
                document.getElementById('mapTitle').textContent = "{{ $log->site['name'] }}";
                
                // Parse coordinates
                if ("{{ $log->latlong }}") {
                    let latlng = "{{ $log->latlong }}".split(',');
                    if (latlng.length === 2) {
                        let lat = parseFloat(latlng[0]);
                        let lng = parseFloat(latlng[1]);
                        
                        if (!isNaN(lat) && !isNaN(lng)) {
                            // Initialize map
                            initializeMap(lat, lng, "{{ $log->site['name'] }}");
                            
                            // Update address in map footer
                            const cacheKey = `${lat},${lng}`;
                            if (geocodedAddresses[cacheKey]) {
                                document.getElementById('mapAddress').innerHTML = 
                                    `<i class="fas fa-location-arrow"></i><span>${geocodedAddresses[cacheKey]}</span>`;
                            } else {
                                document.getElementById('mapAddress').innerHTML = 
                                    `<i class="fas fa-location-arrow"></i><span>Memuat alamat...</span>`;
                                
                                // Fetch if not cached
                                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        let address = data.display_name || 'Alamat tidak tersedia';
                                        geocodedAddresses[cacheKey] = address;
                                        document.getElementById('mapAddress').innerHTML = 
                                            `<i class="fas fa-location-arrow"></i><span>${address}</span>`;
                                    })
                                    .catch(() => {
                                        document.getElementById('mapAddress').innerHTML = 
                                            `<i class="fas fa-location-arrow"></i><span>Alamat tidak tersedia</span>`;
                                    });
                            }
                            
                            // Set up open in Google Maps button
                            document.getElementById('openInGoogleMaps').onclick = function() {
                                window.open(`https://www.google.com/maps/search/?api=1&query=${lat},${lng}`, '_blank');
                            };
                        }
                    }
                }
            }
        @endforeach
    }

    // Initialize map with location
    function initializeMap(lat, lng, name) {
        // If map exists, remove it
        if (map) {
            map.remove();
            map = null;
        }
        
        // Create new map
        map = L.map('map').setView([lat, lng], 16);
        
        // Add tile layer - using Google Maps tiles
        L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        }).addTo(map);
        
        // Create custom marker
        const markerIcon = L.divIcon({
            className: 'custom-marker',
            iconSize: [20, 20],
            iconAnchor: [10, 20],
            popupAnchor: [0, -20]
        });
        
        // Add marker
        currentMarker = L.marker([lat, lng], { icon: markerIcon })
            .addTo(map)
            .bindPopup(`<b>${name}</b>`)
            .openPopup();
        
        // Force map to refresh after modal animation
        setTimeout(() => {
            map.invalidateSize();
        }, 300);
    }

    // Close map modal
    function closeMap() {
        document.getElementById('mapModal').style.display = 'none';
        document.documentElement.style.overflow = ''; // Restore body scrolling
        
        // Clean up map
        if (map) {
            map.remove();
            map = null;
            currentMarker = null;
        }
    }

    // Show filter modal
    function showFilterModal() {
        document.getElementById('filterModal').style.display = 'flex';
        document.documentElement.style.overflow = 'hidden'; // Prevent body scrolling
    }

    // Close filter modal
    function closeFilterModal() {
        document.getElementById('filterModal').style.display = 'none';
        document.documentElement.style.overflow = ''; // Restore body scrolling
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(e) {
        const mapModal = document.getElementById('mapModal');
        const filterModal = document.getElementById('filterModal');
        
        if (e.target === mapModal) {
            closeMap();
        }
        
        if (e.target === filterModal) {
            closeFilterModal();
        }
    });
</script>
@endpush