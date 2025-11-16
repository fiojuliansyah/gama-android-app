@extends('layouts.app')

@section('content')
    <div class="header header-fixed header-logo-center">
        <a href="index.html" class="header-title">Buat Ketidak Hadiran</a>
        <a href="{{ route('home') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
    </div>
    <div class="page-content header-clear-medium">

        <form id="formStore" method="POST" action="{{ route('leave.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="content">

                <div class="file-data pb-4">
                    <input type="file" id="file-upload" name="image"
                        class="upload-file border-highlight rounded-sm" accept="image/*">
                    <p class="upload-file-text color-theme opacity-70 border pt-4 pb-5 rounded-sm"><i
                            class="fa fa-image ms-n3 pe-3"></i>Tambahkan Bukti</p>
                </div>
                <div class="list-group list-custom-large upload-file-data disabled">
                    <img id="image-data" src="/mobile/images/empty.png" class="img-fluid rounded-m mb-4">
                    <div class="disabled">
                        <a href="#" class="border-0">
                            <i class="fa font-14 fa-info-circle color-blue-dark"></i>
                            <span>File Name</span>
                            <strong class="upload-file-name">JS Populated</strong>
                        </a>        
                        <a href="#" class="border-0">
                            <i class="fa font-14 fa-weight-hanging color-brown-dark"></i>
                            <span>File Size</span>
                            <strong class="upload-file-size">JS Populated</strong>
                        </a>        
                        <a href="#" class="border-0">
                            <i class="fa font-14 fa-tag color-red-dark"></i>
                            <span>File Type</span>
                            <strong class="upload-file-type">JS Populated</strong>
                        </a>        
                        <a href="#" class="border-0 pb-4">
                            <i class="fa font-14 fa-clock color-blue-dark"></i>
                            <span>Modified Date</span>
                            <strong class="upload-file-modified">JS Populated</strong>
                        </a>  
                    </div>
                </div>

                <div class="has-borders no-icon mb-4">
                    <label for="form51" class="color-highlight">Tipe</label>
                    <select id="form51" name="type_id" class="form-control">
                        <option value="default" disabled selected>Pilih Tipe</option>
                        @foreach ($types as $type)    
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <em><i class="fa fa-angle-down"></i></em>
                </div>
                <div class="has-borders no-icon mb-4">
                    <label for="form5" class="color-highlight">Tanggal Pengajuan</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="has-borders no-icon mb-4">
                    <label for="form5" class="color-highlight">Tanggal Berakhir</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
                <div class="has-borders no-icon mb-4">
                    <textarea id="form7" name="reason" placeholder="Alasan"></textarea>
                    <label for="form7" class="color-highlight">Alasan</label>
                    <em class="mt-n3">(required)</em>
                </div>
                <div class="has-borders no-icon mb-4">
                    <label for="form6" class="color-highlight">No Darurat</label>
                    <input type="text" name="contact" class="form-control" placeholder="No Darurat">
                </div>
                <button type="submit" id="submitButton" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight" style="width: 100%; border: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
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
        clip: rect(0, 0, 0, 0);
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
            // Tampilkan loading spinner
            loadingSpinner.style.display = 'inline-block';
            // Ubah teks tombol
            buttonText.textContent = 'Memproses...';
            // Nonaktifkan tombol untuk mencegah multiple submit
            submitButton.disabled = true;
            // Tambahkan class untuk efek visual tombol dinonaktifkan
            submitButton.classList.add('opacity-50');
        });
    });
</script>
@endpush