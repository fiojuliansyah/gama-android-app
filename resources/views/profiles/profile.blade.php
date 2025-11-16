@extends('layouts.app')

@section('content')
    <div class="header header-fixed header-logo-center">
        <a href="index.html" class="header-title">Profil</a>
        <a href="{{ route('setting') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
    </div>
    <div class="page-content pt-5">

        <div class="content mb-0">
            <form id="profile-update" class="form" action="{{ route('update.profile') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="mt-2 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Tambah Face Recognition</span>
                        <div>
                            <label for="face-upload"
                                class="btn btn-xs bg-highlight rounded-xl shadow-xl text-uppercase font-900 font-11">
                                <i class="fas fa-image" style="color: white;"></i>
                            </label>
                            <input type="file" id="face-upload" accept="image/*" style="display: none;">
                            <button type="button" id="reset-face"
                                class="btn btn-xs bg-danger rounded-xl shadow-xl text-uppercase font-900 font-11 ml-2"
                                style="display: none;">
                                <i class="fas fa-trash" style="color: white;"></i>
                            </button>
                        </div>
                    </div>

                    <div id="face-processing" class="text-center py-3" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memproses wajah...</p>
                    </div>

                    <div id="face-preview-container" class="text-center mb-3" style="display: none;">
                        <img id="face-preview" class="img-fluid rounded" style="max-height: 200px;" />
                        <canvas id="face-canvas" style="display: none;"></canvas>
                    </div>

                    <div id="face-result" class="mt-2" style="display: none;">
                        <div id="face-success" class="bg-success p-2 rounded text-white text-center mb-2"
                            style="display: none;">
                            <i class="fas fa-check-circle"></i> Face ID berhasil dideteksi
                        </div>
                        <div id="face-error" class="bg-danger p-2 rounded text-white text-center mb-2"
                            style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i> <span id="error-message">Tidak ada wajah
                                terdeteksi</span>
                        </div>
                    </div>

                    <div id="face-indicator" class="mt-3 mb-3"
                        style="display: {{ $user->profile && $user->profile['face_id'] ? 'block' : 'none' }}">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success mr-2" style="width: 15px; height: 15px;"></div>
                            <span class="font-13">Face ID Terdaftar</span>
                        </div>
                    </div>

                    <!-- Hidden inputs for face detection -->
                    <input type="hidden" id="face-descriptor" name="face_id"
                        value="{{ $user->profile['face_descriptor'] ?? '' }}">
                    <input type="hidden" id="face-image-data" name="face_image_data" value="">
                </div>

                <!-- Bagian form lainnya tetap sama -->
                <div class="input-style has-borders hnoas-icon input-style-always-active mb-4">
                    <textarea name="address" class="form-control">{{ $user->profile['address'] ?? '' }}</textarea>
                    <label for="form1" class="color-highlight font-400 font-13">Alamat</label>
                </div>
                <div class="input-style has-borders hnoas-icon input-style-always-active mb-4">
                    <select name="gender" class="form-select">
                        <option value="" disabled>Pilih</option>
                        @if ($user->profile && isset($user->profile['gender']))
                            <option value="laki-laki" {{ $user->profile['gender'] == 'laki-laki' ? 'selected' : '' }}>
                                Laki-laki</option>
                            <option value="perempuan" {{ $user->profile['gender'] == 'perempuan' ? 'selected' : '' }}>
                                Perempuan</option>
                        @else
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        @endif
                    </select>
                    <label for="form1" class="color-highlight font-400 font-13">Jenis Kelamin</label>
                </div>
                <div class="input-style has-borders hnoas-icon input-style-always-active mb-4">
                    <input type="name" name="birth_place" class="form-control"
                        value="{{ $user->profile['birth_place'] ?? '' }}">
                    <label for="form1" class="color-highlight font-400 font-13">Tempat Lahir</label>
                </div>
                <div class="input-style has-borders hnoas-icon input-style-always-active mb-4">
                    <input type="date" name="birth_date" class="form-control"
                        value="{{ $user->profile['birth_date'] ?? '' }}">
                    <label for="form1" class="color-highlight font-400 font-13">Tanggal Lahir</label>
                </div>
                <div class="input-style has-borders hnoas-icon input-style-always-active mb-4">
                    <input type="name" name="mother_name" class="form-control"
                        value="{{ $user->profile['mother_name'] ?? '' }}">
                    <label for="form1" class="color-highlight font-400 font-13">Nama Ibu Kandung</label>
                </div>
                <div class="input-style has-borders hnoas-icon input-style-always-active mb-4">
                    <select name="marriage_status" class="form-select">
                        <option value="" disabled>Pilih</option>
                        @if ($user->profile && isset($user->profile['marriage_status']))
                            <option value="TK-0" {{ $user->profile['marriage_status'] == 'TK-0' ? 'selected' : '' }}>
                                TK-0 : Tidak Kawin (lajang/janda/duda)</option>
                            <option value="TK-1" {{ $user->profile['marriage_status'] == 'TK-1' ? 'selected' : '' }}>
                                TK-1 : Duda/Janda (punya anak 1)</option>
                            <option value="TK-2" {{ $user->profile['marriage_status'] == 'TK-2' ? 'selected' : '' }}>
                                TK-2 : Duda/Janda (punya anak 2)</option>
                            <option value="TK-3" {{ $user->profile['marriage_status'] == 'TK-3' ? 'selected' : '' }}>
                                TK-3 : Duda/Janda (punya anak 3)</option>
                            <option value="K-0" {{ $user->profile['marriage_status'] == 'K-0' ? 'selected' : '' }}>K-0
                                : Kawin</option>
                            <option value="K-1" {{ $user->profile['marriage_status'] == 'K-1' ? 'selected' : '' }}>K-1
                                : Kawin (punya anak 1)</option>
                            <option value="K-2" {{ $user->profile['marriage_status'] == 'K-2' ? 'selected' : '' }}>K-2
                                : Kawin (punya anak 2)</option>
                            <option value="K-3" {{ $user->profile['marriage_status'] == 'K-3' ? 'selected' : '' }}>K-3
                                : Kawin (punya anak 3)</option>
                        @else
                            <option value="TK-0">TK-0 : Tidak Kawin (lajang/janda/duda)</option>
                            <option value="TK-1">TK-1 : Duda/Janda (punya anak 1)</option>
                            <option value="TK-2">TK-2 : Duda/Janda (punya anak 2)</option>
                            <option value="TK-3">TK-3 : Duda/Janda (punya anak 3)</option>
                            <option value="K-0">K-0 : Kawin</option>
                            <option value="K-1">K-1 : Kawin (punya anak 1)</option>
                            <option value="K-2">K-2 : Kawin (punya anak 2)</option>
                            <option value="K-3">K-3 : Kawin (punya anak 3)</option>
                        @endif
                    </select>
                    <label for="form1" class="color-highlight font-400 font-13">Status Pernikahan</label>
                </div>
                <div class="input-style has-borders hnoas-icon input-style-always-active mb-4 mt-4">
                    <input type="name" name="npwp_number" class="form-control"
                        value="{{ $user->profile['npwp_number'] ?? '' }}">
                    <label for="form1" class="color-highlight font-400 font-13">No NPWP</label>
                </div>
            </form>
        </div>
        <a href="#" onclick="event.preventDefault(); document.getElementById('profile-update').submit();"
            class="btn btn-full btn-margins bg-highlight rounded-sm shadow-xl btn-m text-uppercase font-900">Save
            Information</a>

    </div>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script data-navigate-track>
    // Elements
    const faceUpload = document.getElementById('face-upload');
    const facePreviewContainer = document.getElementById('face-preview-container');
    const facePreview = document.getElementById('face-preview');
    const faceCanvas = document.getElementById('face-canvas');
    const faceDescriptorInput = document.getElementById('face-descriptor');
    const faceImageDataInput = document.getElementById('face-image-data');
    const faceIndicator = document.getElementById('face-indicator');
    const faceProcessing = document.getElementById('face-processing');
    const faceResult = document.getElementById('face-result');
    const faceSuccess = document.getElementById('face-success');
    const faceError = document.getElementById('face-error');
    const errorMessage = document.getElementById('error-message');
    const resetFaceButton = document.getElementById('reset-face');

    // State
    let isModelLoaded = false;
    const minFaceSize = 150;
    const outputSize = 400; // Fixed square output size in pixels

    // Load Face-API models
    async function loadModels() {
        try {
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');

            isModelLoaded = true;
            console.log('Face models loaded successfully');

            return true;
        } catch (error) {
            console.error('Error loading face detection models:', error);
            return false;
        }
    }

    // Check if face descriptor already exists
    function checkExistingFaceDescriptor() {
        if (faceDescriptorInput.value) {
            faceIndicator.style.display = 'block';
            resetFaceButton.style.display = 'inline-block';
        }
    }

    // Reset face ID
    function resetFaceId() {
        faceDescriptorInput.value = '';
        faceImageDataInput.value = '';
        faceIndicator.style.display = 'none';
        resetFaceButton.style.display = 'none';
        facePreviewContainer.style.display = 'none';
        faceResult.style.display = 'none';
    }

    // Function to create a square crop of an image focusing on the face
    async function createSquareCrop(img, faceBox) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Get original image dimensions
        const imgWidth = img.naturalWidth || img.width;
        const imgHeight = img.naturalHeight || img.height;

        // Determine crop size (use the larger of width or height of the face detection box)
        const faceSize = Math.max(faceBox.width, faceBox.height);

        // Add some padding around the face (50% extra space)
        const cropSize = Math.min(imgWidth, imgHeight, Math.round(faceSize * 1.5));

        // Calculate center of the face
        const centerX = faceBox.x + faceBox.width / 2;
        const centerY = faceBox.y + faceBox.height / 2;

        // Calculate top left corner of crop
        let cropX = centerX - cropSize / 2;
        let cropY = centerY - cropSize / 2;

        // Ensure crop remains within image boundaries
        cropX = Math.max(0, Math.min(imgWidth - cropSize, cropX));
        cropY = Math.max(0, Math.min(imgHeight - cropSize, cropY));

        // Set canvas to square dimensions
        canvas.width = outputSize;
        canvas.height = outputSize;

        // Draw cropped image to canvas as square
        ctx.drawImage(
            img,
            cropX, cropY, cropSize, cropSize, // Source crop
            0, 0, outputSize, outputSize // Destination square
        );

        return canvas;
    }

    // Function to create a square image even when no face is detected
    function createDefaultSquareCrop(img) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Get original image dimensions
        const imgWidth = img.naturalWidth || img.width;
        const imgHeight = img.naturalHeight || img.height;

        // Determine the crop size (use the smaller dimension to ensure we stay within the image)
        const cropSize = Math.min(imgWidth, imgHeight);

        // Calculate crop coordinates (centered)
        const cropX = (imgWidth - cropSize) / 2;
        const cropY = (imgHeight - cropSize) / 2;

        // Set canvas to square dimensions
        canvas.width = outputSize;
        canvas.height = outputSize;

        // Draw cropped image to canvas as square
        ctx.drawImage(
            img,
            cropX, cropY, cropSize, cropSize, // Source crop
            0, 0, outputSize, outputSize // Destination square
        );

        return canvas;
    }

    // Process uploaded image with square cropping
    async function processImage(file) {
        if (!file) return;

        // Reset states
        facePreviewContainer.style.display = 'block';
        faceProcessing.style.display = 'block';
        faceResult.style.display = 'none';

        // Create a URL for the selected file
        const imageURL = URL.createObjectURL(file);

        // Show preview of the original image
        facePreview.src = imageURL;

        // Wait for models to load if they haven't
        if (!isModelLoaded) {
            const modelsLoaded = await loadModels();
            if (!modelsLoaded) {
                faceProcessing.style.display = 'none';
                faceResult.style.display = 'block';
                faceError.style.display = 'block';
                faceSuccess.style.display = 'none';
                errorMessage.textContent = 'Gagal memuat model deteksi wajah';
                return;
            }
        }

        try {
            // Wait for the image to load
            await new Promise(resolve => {
                facePreview.onload = resolve;
            });

            // Detect faces in the image
            const detections = await faceapi.detectAllFaces(facePreview)
                .withFaceLandmarks()
                .withFaceDescriptors();

            let squareCropCanvas;

            if (detections.length === 0) {
                // If no face is detected, still create a square image (centered crop)
                squareCropCanvas = createDefaultSquareCrop(facePreview);

                // Show warning but continue with the square crop
                faceError.style.display = 'block';
                faceSuccess.style.display = 'none';
                errorMessage.textContent = 'Tidak ada wajah terdeteksi, menggunakan crop otomatis';
            } else if (detections.length > 1) {
                // Multiple faces - use the largest face for cropping
                let largestFaceIndex = 0;
                let maxFaceSize = 0;

                detections.forEach((detection, index) => {
                    const faceSize = detection.detection.box.width * detection.detection.box.height;
                    if (faceSize > maxFaceSize) {
                        maxFaceSize = faceSize;
                        largestFaceIndex = index;
                    }
                });

                // Create square crop centered on the largest face
                squareCropCanvas = await createSquareCrop(facePreview, detections[largestFaceIndex].detection.box);

                // Show warning but continue with the square crop
                faceError.style.display = 'block';
                faceSuccess.style.display = 'none';
                errorMessage.textContent = 'Terdeteksi beberapa wajah, menggunakan wajah terbesar';
            } else {
                // Single face detected - check if face is large enough
                const faceSize = detections[0].detection.box.width;
                if (faceSize < minFaceSize) {
                    // Face too small, but still create a square crop
                    squareCropCanvas = await createSquareCrop(facePreview, detections[0].detection.box);

                    // Show warning but continue with the square crop
                    faceError.style.display = 'block';
                    faceSuccess.style.display = 'none';
                    errorMessage.textContent = 'Wajah terlalu kecil, kualitas mungkin kurang baik';
                } else {
                    // Ideal case: one face of good size
                    squareCropCanvas = await createSquareCrop(facePreview, detections[0].detection.box);

                    // Show success
                    faceError.style.display = 'none';
                    faceSuccess.style.display = 'block';
                }
            }

            // Setup canvas for display
            faceCanvas.width = squareCropCanvas.width;
            faceCanvas.height = squareCropCanvas.height;
            const ctx = faceCanvas.getContext('2d');

            // Draw the square cropped image to the visible canvas
            ctx.drawImage(squareCropCanvas, 0, 0);

            // Update preview with the square crop
            facePreview.src = squareCropCanvas.toDataURL('image/jpeg', 0.9);

            // Re-detect face on the cropped image for more accurate descriptor
            const croppedDetections = await faceapi.detectAllFaces(squareCropCanvas)
                .withFaceLandmarks()
                .withFaceDescriptors();

            if (croppedDetections.length > 0) {
                // Draw only the detection box on the square image (removed landmarks drawing)
                // faceapi.draw.drawDetections(faceCanvas, croppedDetections);
                // Removed: faceapi.draw.drawFaceLandmarks(faceCanvas, croppedDetections);

                // Store the face descriptor from the cropped image
                const faceDescriptorArray = Array.from(croppedDetections[0].descriptor);
                faceDescriptorInput.value = JSON.stringify(faceDescriptorArray);
            } else if (detections.length > 0) {
                // If no face detected in cropped image, use descriptor from original image
                const faceDescriptorArray = Array.from(detections[0].descriptor);
                faceDescriptorInput.value = JSON.stringify(faceDescriptorArray);
            } else {
                // No face descriptor available
                faceDescriptorInput.value = '';
                faceError.style.display = 'block';
                faceSuccess.style.display = 'none';
                errorMessage.textContent = 'Tidak dapat mengekstrak data wajah untuk pengenalan';
            }

            // Get image data as base64 for upload regardless of face detection
            const imageData = faceCanvas.toDataURL('image/jpeg', 0.9);
            faceImageDataInput.value = imageData;

            // Always show indicators since we have a square image
            faceProcessing.style.display = 'none';
            faceResult.style.display = 'block';
            faceIndicator.style.display = 'block';
            resetFaceButton.style.display = 'inline-block';

            console.log('Image processed and cropped to square format');

        } catch (error) {
            console.error('Error processing image:', error);
            faceProcessing.style.display = 'none';
            faceResult.style.display = 'block';
            faceError.style.display = 'block';
            faceSuccess.style.display = 'none';
            errorMessage.textContent = 'Gagal memproses gambar. Coba gunakan foto lain';
        }
    }

    // Handle errors with better logging for WebView
    function handleError(error, context) {
        console.error(`Error in ${context}:`, error);
        if (typeof error === 'object' && error !== null) {
            console.error('Error details:', {
                message: error.message,
                name: error.name,
                stack: error.stack
            });
        }
    }

    // Event listeners with error handling
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Initialize by loading models and checking for existing face descriptor
            loadModels().catch(error => handleError(error, 'loadModels'));
            checkExistingFaceDescriptor();

            // Handle file upload
            faceUpload.addEventListener('change', (event) => {
                try {
                    const file = event.target.files[0];
                    if (file) {
                        console.log('File selected:', file.name, file.type, file.size);
                        processImage(file).catch(error => handleError(error, 'processImage'));
                    }
                } catch (error) {
                    handleError(error, 'file upload event');
                }
            });

            // Handle reset button
            resetFaceButton.addEventListener('click', () => {
                try {
                    resetFaceId();
                } catch (error) {
                    handleError(error, 'resetFaceId');
                }
            });

            console.log('Face recognition initialized successfully');
        } catch (error) {
            handleError(error, 'DOMContentLoaded');
        }
    });
</script>
@endpush
