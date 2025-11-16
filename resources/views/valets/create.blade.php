@extends('layouts.app')

@section('content')
    <div class="header header-fixed header-logo-center">
        <a href="index.html" class="header-title">Buat Transaksi Valet</a>
        <a href="{{ route('valet.index') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
    </div>
    <div class="page-content header-clear-medium">

        <form id="formStore" method="POST" action="{{ route('valet.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="content">
                <div class="input-style has-borders no-icon mb-4">
                    <label for="form1" class="color-highlight">Nama Customer</label>
                    <input type="text" name="name" class="form-control" placeholder="Biarkan kosong kalau tidak ada">
                </div>
                <div class="input-style has-borders no-icon mb-4">
                    <label for="form1" class="color-highlight">Plat Nomor</label>
                    <input type="text" name="plat_number" class="form-control" placeholder="Nomor Plat Kendaraan" required>
                </div>

                <div class="input-style has-borders no-icon mb-4">
                    <label for="form2" class="color-highlight">Jumlah Pembayaran</label>
                    <input type="text" name="amount" class="form-control" placeholder="Jumlah Pembayaran" required>
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
