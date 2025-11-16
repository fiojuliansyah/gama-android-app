@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <a href="{{ route('findings-reports.index') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
    <span class="header-title">Buat Findings Report</span>
</div>

<div class="page-content header-clear-medium">
    <form id="formStore" method="POST" action="{{ route('findings-reports.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="content">

            <!-- Upload Image -->
            <div class="file-data pb-4">
                <input type="file" id="file-upload" name="image" class="upload-file border-highlight rounded-sm" accept="image/*" onchange="previewImage()">
                <p class="upload-file-text color-theme opacity-70 border pt-4 pb-5 rounded-sm">
                    <i class="fa fa-image ms-n3 pe-3"></i>Tambahkan Bukti
                </p>
            </div>

            <!-- Preview Image -->
            <div class="list-group list-custom-large upload-file-data">
                <img id="image-data" src="/assets/images/empty.png" class="img-fluid rounded-m mb-4" width="100">
            </div>

            <!-- Title -->
            <div class="has-borders no-icon mb-4">
                <label for="title" class="color-highlight">Judul</label>
                <input type="text" name="title" class="form-control" id="title" placeholder="Judul laporan" required>
            </div>

            <!-- Date -->
            <div class="has-borders no-icon mb-4">
                <label for="date" class="color-highlight">Tanggal</label>
                <input type="date" name="date" class="form-control" id="date" required>
            </div>

            <!-- Description -->
            <div class="has-borders no-icon mb-4">
                <label for="description" class="color-highlight">Deskripsi</label>
                <textarea id="description" name="description" class="form-control" placeholder="Deskripsi laporan" required></textarea>
            </div>

            <!-- Location -->
            <div class="has-borders no-icon mb-4">
                <label for="location" class="color-highlight">Lokasi</label>
                <input type="text" name="location" class="form-control" id="location" placeholder="Lokasi" required>
            </div>

            <!-- Direct Action -->
            <div class="has-borders no-icon mb-4">
                <label for="direct_action" class="color-highlight">Tindakan Langsung</label>
                <input type="text" name="direct_action" class="form-control" id="direct_action" placeholder="Tindakan langsung" required>
            </div>

            <!-- Type -->
            <div class="has-borders no-icon mb-4">
                <label for="type" class="color-highlight">Tipe</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="" disabled selected>Pilih Tipe</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <!-- Status -->
            <div class="has-borders no-icon mb-4">
                <label for="status" class="color-highlight">Status</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="" disabled selected>Pilih Status</option>
                    <option value="pending">Pending</option>
                    <option value="solved">Solved</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitButton" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight d-flex align-items-center justify-content-center gap-2">
                <span>Simpan</span>
                <div id="loadingSpinner" class="spinner-border spinner-border-sm" style="display: none;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        </div>
    </form>
</div>
@endsection

@push('css')
<style>
.spinner-border {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 0.2em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border 0.75s linear infinite;
}
@keyframes spinner-border {
    to { transform: rotate(360deg); }
}
.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    margin: -1px;
    padding: 0;
    overflow: hidden;
    clip: rect(0,0,0,0);
    white-space: nowrap;
    border: 0;
}
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formStore');
    const submitButton = document.getElementById('submitButton');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const buttonText = submitButton.querySelector('span');

    form.addEventListener('submit', function() {
        loadingSpinner.style.display = 'inline-block';
        buttonText.textContent = 'Memproses...';
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50');
    });
});

// Preview Image Function
function previewImage() {
    const file = document.getElementById('file-upload').files[0];
    const preview = document.getElementById('image-data');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
