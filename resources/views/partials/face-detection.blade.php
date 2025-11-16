@push('js')
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const videoElement = document.getElementById('video-element');
            const faceOverlay = document.getElementById('face-overlay');
            const submitBtn = document.getElementById('submit-btn');
            const attendanceForm = document.getElementById('attendance-form');
            const latlongInput = document.getElementById('latlong-input');
            const imageInput = document.getElementById('image-input');
            const locationStatus = document.getElementById('location-status');
            const locationName = document.getElementById('location-name');
            const currentTime = document.getElementById('current-time');
            const currentDate = document.getElementById('current-date');
            const loadingModels = document.getElementById('loading-models');
            const lookingForFace = document.getElementById('looking-for-face');
            const faceDetected = document.getElementById('face-detected');
            const verificationResult = document.getElementById('verification-result');
            const verificationSuccess = document.getElementById('verification-success');
            const verificationError = document.getElementById('verification-error');
            const errorMessage = document.getElementById('error-message');
            const faceAnimationGuide = document.getElementById('face-animation-guide');
            const animationText = document.getElementById('animation-text');

            // Hide submit button (already hidden in HTML, but this is a safety measure)
            submitBtn.style.display = 'none';

            // Variables
            let isModelLoaded = false;
            let stream = null;
            let detectionInterval = null;
            let userFaceDescriptor = null;
            let userLatitude = null;
            let userLongitude = null;
            let faceVerified = false;
            let locationVerified = false;
            let submissionInProgress = false;
            let animationPhase = 0;
            let locationWatcher = null;
            let geoPollingAttempts = 0;
            const maxGeoPollingAttempts = 5;

            // Detect if running in WebView
            function isRunningInWebView() {
                return (/(android|iphone|ipod|ipad)/i.test(navigator.userAgent) && 
                        window.navigator.userAgent.indexOf('wv') > -1) || 
                       (/(iphone|ipod|ipad)/i.test(navigator.userAgent) && 
                        window.navigator.standalone === true);
            }

            // Get user face descriptor
            function getUserFaceDescriptor() {
                @if (Auth::user()->profile && isset(Auth::user()->profile['face_descriptor']))
                    try {
                        // Properly parse the stored descriptor and convert to Float32Array
                        const descriptorData = JSON.parse('{{ Auth::user()->profile['face_descriptor'] }}');
                        
                        // Log for debugging
                        console.log('Loaded face descriptor data:', descriptorData);
                        
                        // Ensure it's the correct format
                        if (Array.isArray(descriptorData) && descriptorData.length === 128) {
                            return new Float32Array(descriptorData);
                        } else {
                            console.error('Invalid face descriptor format, expected array of 128 values');
                            return null;
                        }
                    } catch (e) {
                        console.error('Error parsing face descriptor:', e);
                        return null;
                    }
                @else
                    console.warn('No face descriptor found for user');
                    return null;
                @endif
            }

            // Update date and time
            function updateDateTime() {
                const now = new Date();
                currentTime.textContent = now.toLocaleTimeString('id-ID');
                currentDate.textContent = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            // Update face animation instructions
            function updateFaceAnimation(phase) {
                switch (phase) {
                    case 0:
                        animationText.textContent = "Gerakan wajah Anda perlahan";
                        break;
                    case 1:
                        animationText.textContent = "Gerakan ke kiri dan kanan";
                        break;
                    case 2:
                        animationText.textContent = "Gerakan ke atas dan bawah";
                        break;
                    case 3:
                        animationText.textContent = "Berikan ekspresi senyum";
                        break;
                    default:
                        animationText.textContent = "Gerakan wajah Anda perlahan";
                }
            }

            async function loadModels() {
                try {
                    await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
                    await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                    await faceapi.nets.faceRecognitionNet.loadFromUri('/models');

                    isModelLoaded = true;
                    loadingModels.style.display = 'none';
                    lookingForFace.style.display = 'block';
                    console.log('Face detection models loaded successfully');

                    return true;
                } catch (error) {
                    console.error('Error loading face detection models:', error);
                    errorMessage.textContent = 'Gagal memuat model AI. Refresh halaman untuk mencoba kembali.';
                    verificationError.style.display = 'block';
                    verificationResult.style.display = 'block';
                    loadingModels.style.display = 'none';
                    return false;
                }
            }

            async function startCamera() {
                try {
                    // Get list of all video devices
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    const videoDevices = devices.filter(device => device.kind === 'videoinput');
                    
                    // Try to select a physical camera - usually the first one that's not OBS or other virtual cameras
                    let selectedDevice = videoDevices[0]; // Default to first camera
                    
                    for (const device of videoDevices) {
                        // Attempt to find a physical camera by filtering out common virtual camera names
                        const deviceName = device.label.toLowerCase();
                        if (!deviceName.includes('obs') && 
                            !deviceName.includes('virtual') && 
                            !deviceName.includes('capture') &&
                            !deviceName.includes('screen')) {
                            selectedDevice = device;
                            break;
                        }
                    }
                    
                    // Perbaikan konstraints untuk mengurangi zoom kamera
                    const constraints = {
                        video: {
                            facingMode: 'user',
                            width: { min: 640, ideal: 1280, max: 1920 },
                            height: { min: 480, ideal: 720, max: 1080 },
                            advanced: [
                                { zoom: 1.0 } // Zoom minimum/normal
                            ]
                        }
                    };
                    
                    // If we found a specific camera device, use it
                    if (selectedDevice && selectedDevice.deviceId) {
                        constraints.video.deviceId = { exact: selectedDevice.deviceId };
                    }
                    
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                    videoElement.srcObject = stream;

                    // Coba atur zoom level jika didukung
                    const videoTrack = stream.getVideoTracks()[0];
                    if (videoTrack && typeof videoTrack.getCapabilities === 'function') {
                        const capabilities = videoTrack.getCapabilities();
                        if (capabilities.zoom) {
                            try {
                                const zoomConstraints = { advanced: [{ zoom: 1.0 }] };
                                await videoTrack.applyConstraints(zoomConstraints);
                                console.log('Zoom level set to 1.0');
                            } catch (e) {
                                console.warn('Failed to set zoom level:', e);
                            }
                        }
                    }

                    // Wait for video metadata to load before proceeding
                    await new Promise(resolve => {
                        videoElement.onloadedmetadata = () => {
                            // Match canvas size to window dimensions
                            faceOverlay.width = window.innerWidth;
                            faceOverlay.height = window.innerHeight;
                            resolve();
                        };
                    });

                    return true;
                } catch (error) {
                    console.error('Error accessing camera:', error);
                    errorMessage.textContent =
                        'Gagal mengakses kamera. Harap berikan izin kamera dan refresh halaman.';
                    verificationError.style.display = 'block';
                    verificationResult.style.display = 'block';
                    loadingModels.style.display = 'none';
                    return false;
                }
            }

            // Show Android location help for WebView users
            function showAndroidLocationHelp() {
                const helpBox = document.createElement('div');
                helpBox.className = 'alert alert-warning';
                helpBox.style.position = 'absolute';
                helpBox.style.top = '50%';
                helpBox.style.left = '50%';
                helpBox.style.transform = 'translate(-50%, -50%)';
                helpBox.style.zIndex = '100';
                helpBox.style.width = '80%';
                helpBox.style.maxWidth = '320px';
                helpBox.innerHTML = `
                    <h5>Petunjuk Akses Lokasi:</h5>
                    <ul>
                        <li>Pastikan GPS perangkat Anda aktif</li>
                        <li>Pastikan aplikasi memiliki izin akses lokasi</li>
                        <li>Jika dalam ruangan, cobalah mendekati jendela</li>
                    </ul>
                    <button class="btn btn-sm btn-warning w-100">Saya Mengerti</button>
                `;
                
                document.body.appendChild(helpBox);
                
                helpBox.querySelector('button').onclick = function() {
                    helpBox.remove();
                    getLocation(); // Coba lagi
                };
            }

            // Function to add location refresh button
            function addLocationRefreshButton() {
                const refreshButton = document.createElement('button');
                refreshButton.textContent = 'Coba lagi';
                refreshButton.className = 'btn btn-sm btn-warning mt-1';
                refreshButton.onclick = function() {
                    this.textContent = 'Mencoba ulang...';
                    this.disabled = true;
                    setTimeout(() => {
                        this.textContent = 'Coba lagi';
                        this.disabled = false;
                    }, 3000);
                    getLocation();
                };
                
                // Hapus tombol lama jika ada
                const existingButton = locationName.querySelector('button');
                if (existingButton) {
                    existingButton.remove();
                }
                
                locationName.appendChild(document.createElement('br'));
                locationName.appendChild(refreshButton);
            }

            // Request permission explicitly (for WebView)
            function requestLocationPermission() {
                const permissionBtn = document.createElement('button');
                permissionBtn.textContent = 'Izinkan Akses Lokasi';
                permissionBtn.className = 'btn btn-primary btn-block mx-auto my-3';
                permissionBtn.style.width = '80%';
                permissionBtn.style.maxWidth = '300px';
                permissionBtn.style.display = 'block';
                permissionBtn.style.zIndex = '50';
                
                permissionBtn.onclick = function() {
                    this.textContent = 'Meminta izin...';
                    this.disabled = true;
                    getLocation();
                    
                    // Remove button after a short delay
                    setTimeout(() => {
                        this.remove();
                    }, 2000);
                };
                
                // Tambahkan ke overlay
                document.querySelector('.overlay-status').prepend(permissionBtn);
            }

            // Polling location for problematic WebViews
            function pollGeolocation() {
                if (geoPollingAttempts >= maxGeoPollingAttempts) {
                    locationStatus.innerHTML = '<span class="badge bg-danger">Gagal mendapatkan lokasi setelah beberapa percobaan</span>';
                    return;
                }
                
                geoPollingAttempts++;
                locationStatus.innerHTML = `<span class="badge bg-secondary">Mencoba lokasi (${geoPollingAttempts}/${maxGeoPollingAttempts})</span>`;
                
                // Coba dapatkan lokasi dengan timeout lebih lama setiap percobaan
                const timeoutValue = 5000 + (geoPollingAttempts * 2000);
                setTimeout(function() {
                    // Jika masih belum dapat lokasi, polling lagi
                    if (!userLatitude || !userLongitude) {
                        getLocation();
                        if (geoPollingAttempts < maxGeoPollingAttempts) {
                            pollGeolocation();
                        }
                    }
                }, timeoutValue);
            }

            // Start and manage location watcher
            function startLocationWatcher() {
                if (navigator.geolocation && !locationWatcher) {
                    const options = {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    };
                    
                    try {
                        locationWatcher = navigator.geolocation.watchPosition(
                            (position) => {
                                userLatitude = position.coords.latitude;
                                userLongitude = position.coords.longitude;
                                
                                // Update input dan UI
                                latlongInput.value = `${userLatitude},${userLongitude}`;
                                locationStatus.innerHTML = '<span class="badge bg-success">Lokasi diperbarui</span>';
                                locationName.textContent = `Lat: ${userLatitude.toFixed(6)}, Long: ${userLongitude.toFixed(6)}`;
                                
                                // Verify location
                                verifyLocation(position);
                            },
                            (error) => {
                                console.error('Watcher error:', error);
                                if (locationWatcher) {
                                    navigator.geolocation.clearWatch(locationWatcher);
                                    locationWatcher = null;
                                    
                                    // Try to get location once more
                                    setTimeout(getLocation, 2000);
                                }
                            },
                            options
                        );
                        console.log('Location watcher started');
                    } catch (e) {
                        console.error('Failed to start location watcher:', e);
                    }
                }
            }

            function stopLocationWatcher() {
                if (locationWatcher !== null) {
                    navigator.geolocation.clearWatch(locationWatcher);
                    locationWatcher = null;
                    console.log('Location watcher stopped');
                }
            }

            // Get geolocation with improved WebView support
            function getLocation() {
                if (navigator.geolocation) {
                    // Options with high accuracy for better results
                    const options = {
                        enableHighAccuracy: true,
                        timeout: isRunningInWebView() ? 20000 : 10000, // Higher timeout for WebView
                        maximumAge: 0
                    };
                    
                    // Show loading indicator
                    locationStatus.innerHTML = '<span class="badge bg-secondary">Mencari lokasi...</span>';
                    
                    // If in WebView, show more guidance
                    if (isRunningInWebView()) {
                        locationName.textContent = "Mendapatkan lokasi... Pastikan GPS aktif dan izin lokasi diberikan";
                    }
                    
                    navigator.geolocation.getCurrentPosition(
                        // Success callback
                        (position) => {
                            userLatitude = position.coords.latitude;
                            userLongitude = position.coords.longitude;

                            console.log('Location found:', userLatitude, userLongitude);

                            // Store to input
                            latlongInput.value = `${userLatitude},${userLongitude}`;

                            // Update UI
                            locationStatus.innerHTML = '<span class="badge bg-success">Lokasi ditemukan</span>';
                            locationName.textContent = `Lat: ${userLatitude.toFixed(6)}, Long: ${userLongitude.toFixed(6)}`;

                            // Verify location if site radius checking is required
                            verifyLocation(position);
                            
                            // Start continuous location updates
                            if (!locationWatcher) {
                                startLocationWatcher();
                            }
                        },
                        // Error callback with better handling
                        (error) => {
                            console.error('Error getting location:', error);
                            locationStatus.innerHTML = '<span class="badge bg-danger">Gagal mendapatkan lokasi</span>';

                            // For WebView on Android, show special help
                            if (isRunningInWebView() && /android/i.test(navigator.userAgent)) {
                                showAndroidLocationHelp();
                                return;
                            }

                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    locationName.textContent = "Izin lokasi ditolak. Aktifkan layanan lokasi di pengaturan dan refresh halaman.";
                                    addLocationRefreshButton();
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    locationName.textContent = "Informasi lokasi tidak tersedia. Pastikan GPS aktif.";
                                    addLocationRefreshButton();
                                    break;
                                case error.TIMEOUT:
                                    locationName.textContent = "Permintaan lokasi timeout. Coba lagi.";
                                    addLocationRefreshButton();
                                    break;
                                default:
                                    locationName.textContent = "Terjadi kesalahan saat mendapatkan lokasi. Coba lagi.";
                                    addLocationRefreshButton();
                                    break;
                            }
                            
                            // Try a different approach for WebView if we get an error
                            if (isRunningInWebView() && geoPollingAttempts < maxGeoPollingAttempts) {
                                pollGeolocation();
                            }
                        },
                        options // Use the options we defined
                    );
                } else {
                    locationStatus.innerHTML = '<span class="badge bg-danger">Tidak mendukung Geolokasi</span>';
                    locationName.textContent = "Browser Anda tidak mendukung geolokasi.";
                }
            }

            // Auto-submit if conditions are met
            function checkAndSubmit() {
                // Prevent multiple submissions
                if (submissionInProgress) {
                    return;
                }

                // Check if both face and location (when needed) are verified
                if (faceVerified && (locationVerified || {{ Auth::user()->department_id }} == 2)) {
                    submissionInProgress = true;
                    
                    // Show verification success for 1 second before submitting
                    setTimeout(() => {
                        // Submit the form
                        attendanceForm.submit();
                    }, 1000); // 1-second delay to show success message
                }
            }

            // Detection loop
            function startFaceDetection() {
                const ctx = faceOverlay.getContext('2d');
                let consecutiveDetections = 0;
                let animationTimer = setInterval(() => {
                    animationPhase = (animationPhase + 1) % 4;
                    updateFaceAnimation(animationPhase);
                }, 3000);

                // Add window resize handler
                window.addEventListener('resize', () => {
                    faceOverlay.width = window.innerWidth;
                    faceOverlay.height = window.innerHeight;
                });

                detectionInterval = setInterval(async () => {
                    if (videoElement.readyState === 4 && isModelLoaded && !submissionInProgress) {
                        try {
                            // Clear canvas
                            ctx.clearRect(0, 0, faceOverlay.width, faceOverlay.height);

                            // Get the full window dimensions for display
                            const displayWidth = window.innerWidth;
                            const displayHeight = window.innerHeight;

                            // Update canvas dimensions
                            faceOverlay.width = displayWidth;
                            faceOverlay.height = displayHeight;

                            // Display size for face detection
                            const displaySize = {
                                width: displayWidth,
                                height: displayHeight
                            };

                            // Detect faces
                            const detections = await faceapi.detectAllFaces(videoElement)
                                .withFaceLandmarks()
                                .withFaceDescriptors();

                            if (detections.length === 0) {
                                lookingForFace.style.display = 'block';
                                faceDetected.style.display = 'none';
                                faceAnimationGuide.style.display = 'block';
                                consecutiveDetections = 0;
                                return;
                            }

                            if (detections.length > 1) {
                                errorMessage.textContent =
                                    'Terdeteksi lebih dari satu wajah. Pastikan hanya Anda yang terlihat di kamera.';
                                verificationError.style.display = 'block';
                                verificationResult.style.display = 'block';
                                return;
                            }

                            // Count consecutive successful detections
                            consecutiveDetections++;
                            
                            // After 5 consecutive detections (0.5 seconds), hide the animation guide
                            if (consecutiveDetections >= 5) {
                                faceAnimationGuide.style.display = 'none';
                                clearInterval(animationTimer); // Stop animation cycle
                            }

                            // Draw box around face
                            lookingForFace.style.display = 'none';
                            faceDetected.style.display = 'block';

                            // Resize detections to match the displayed video size
                            const resizedDetections = faceapi.resizeResults(detections, displaySize);

                            // Draw only the face detection box (square)
                            faceapi.draw.drawDetections(faceOverlay, resizedDetections);

                            // Verify face with stored descriptor
                            if (userFaceDescriptor) {
                                const detectedDescriptor = detections[0].descriptor;
                                const distance = faceapi.euclideanDistance(detectedDescriptor,
                                    userFaceDescriptor);

                                // Threshold for face matching (adjust as needed)
                                const threshold = 0.5;

                                if (distance < threshold) {
                                    // Face verified
                                    if (!faceVerified) {
                                        faceVerified = true;
                                        verificationSuccess.style.display = 'block';
                                        verificationResult.style.display = 'block';
                                        faceAnimationGuide.style.display = 'none'; // Hide animation when verified

                                        // Capture image for submission
                                        captureImage();

                                        // Try to auto-submit
                                        checkAndSubmit();
                                    }
                                } else {
                                    // Face not verified
                                    errorMessage.textContent = `Wajah tidak cocok dengan data yang terdaftar.`;
                                    verificationError.style.display = 'block';
                                    verificationResult.style.display = 'block';
                                }
                            }
                        } catch (error) {
                            console.error('Error during face detection:', error);
                        }
                    }
                }, 100);
            }

            // Capture image for submission
            function captureImage() {
                const canvas = document.createElement('canvas');
                canvas.width = videoElement.videoWidth;
                canvas.height = videoElement.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

                // Mengambil gambar sebagai base64
                const imageData = canvas.toDataURL('image/jpeg', 0.8);
                
                // Memastikan gambar disimpan dalam input image
                imageInput.value = imageData;
            }

            // Verify location against site radius
            function verifyLocation(position) {
                @if (Auth::user()->department_id == 2)
                    // Skip location check for department ID 2
                    locationVerified = true;
                    
                    // If face is already verified, try to submit
                    if (faceVerified) {
                        checkAndSubmit();
                    }
                @else
                    const siteLat = {{ Auth::user()->site->lat ?? 0 }};
                    const siteLong = {{ Auth::user()->site->long ?? 0 }};
                    const siteRadius = {{ Auth::user()->site->radius ?? 0 }};

                    // Calculate distance between user and site
                    function calculateDistance(lat1, lon1, lat2, lon2) {
                        const R = 6371e3; // Earth radius in meters
                        const φ1 = lat1 * Math.PI / 180;
                        const φ2 = lat2 * Math.PI / 180;
                        const Δφ = (lat2 - lat1) * Math.PI / 180;
                        const Δλ = (lon2 - lon1) * Math.PI / 180;

                        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                            Math.cos(φ1) * Math.cos(φ2) *
                            Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
                        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                        return R * c; // Distance in meters
                    }

                    const distance = calculateDistance(
                        position.coords.latitude,
                        position.coords.longitude,
                        siteLat,
                        siteLong
                    );

                    if (distance <= siteRadius) {
                        locationVerified = true;
                        locationName.textContent += ` (${Math.round(distance)}m dari lokasi)`;
                        
                        // If face is already verified, try to auto-submit
                        if (faceVerified) {
                            checkAndSubmit();
                        }
                    } else {
                        locationName.textContent +=
                            ` (${Math.round(distance)}m dari lokasi, di luar radius ${siteRadius}m)`;
                        locationStatus.innerHTML = '<span class="badge bg-danger">Di luar lokasi</span>';
                    }
                @endif
            }

            // Initialize
            async function initialize() {
                // Output info about environment
                console.log('User Agent:', navigator.userAgent);
                console.log('Is WebView:', isRunningInWebView());
                
                // Update time
                updateDateTime();
                setInterval(updateDateTime, 1000);

                // Initialize animation phase
                animationPhase = 0;
                updateFaceAnimation(animationPhase);

                // Get user face descriptor
                userFaceDescriptor = getUserFaceDescriptor();

                if (!userFaceDescriptor) {
                    errorMessage.textContent =
                        'Anda belum mendaftarkan Face ID. Silakan daftarkan di halaman profil.';
                    verificationError.style.display = 'block';
                    verificationResult.style.display = 'block';
                    loadingModels.style.display = 'none';
                    faceAnimationGuide.style.display = 'none';
                    return;
                }

                if (isRunningInWebView()) {
                    requestLocationPermission();
                } else {
                    getLocation();
                }

                // Start camera and load models
                const cameraStarted = await startCamera();

                if (cameraStarted) {
                    await loadModels();
                    startFaceDetection();
                }
            }

            // Start initialization
            initialize();

            // Clean up on page unload
            window.addEventListener('beforeunload', function() {
                if (detectionInterval) {
                    clearInterval(detectionInterval);
                }

                if (stream) {
                    stream.getTracks().forEach(function(track) {
                        track.stop();
                    });
                }
                
                // Stop location watcher
                stopLocationWatcher();
            });
        });
    </script>
@endpush