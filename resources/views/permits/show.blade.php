@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <span class="header-title">Form Pengajuan Izin</span>
    <a href="{{ route('home') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
</div>

<div class="page-content pt-5">
    @if(isset($permit) && $permit)
        <div class="list-group list-custom-large">
            <div class="card card-style mb-3">
                <div class="content">
                    <!-- Logo -->
                    <div class="text-center mb-3">
                        <img src="{{ $permit->site->company['logo_url'] }}" alt="Logo" width="150px">
                    </div>

                    <!-- Tanggal & Dibuat Oleh -->
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Tanggal Dibuat</p>
                            <p class="line-height-s">{{ $permit->created_at->format('d-M-Y') }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="color-theme font-15 font-800">Tanggal Pengajuan</p>
                            <p class="line-height-s">{{ $permit->start_date->format('d-M-Y') }}</p>
                        </div>
                    </div>

                    <!-- Nama & Selesai -->
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Dibuat Oleh</p>
                            <p class="line-height-s">{{ $permit->user['name'] }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="color-theme font-15 font-800">Selesai</p>
                            <p class="line-height-s">{{ $permit->end_date->format('d-M-Y') }}</p>
                        </div>
                    </div>

                    <!-- Area & Jabatan -->
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Area</p>
                            <p class="line-height-s">{{ $permit->user->site['name'] }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="color-theme font-15 font-800">Jabatan</p>
                            @if (!empty($permit->user->getRoleNames()))
                                @foreach ($permit->user->getRoleNames() as $role)
                                    <p class="line-height-s">{{ $role }}</p>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mt-3 mb-3">
                        <h6>Deskripsi</h6>
                        <p>{{ $permit->reason }}</p>
                    </div>

                    <!-- Approval -->
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Menyetujui</p>
                            <p class="line-height-s">{{ $permit->user->leader['name'] ?? '-' }}</p>
                        </div>
                        <div class="col-6 text-end">
                            @if($permit->user->leader->leader)
                                <p class="color-theme font-15 font-800">Menyetujui</p>
                                <p class="line-height-s">{{ $permit->user->leader->leader['name'] ?? '-' }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Image -->
                    @if($permit->image_url)
                        <div class="mt-3 text-center">
                            <img src="{{ $permit->image_url }}" alt="Bukti Izin" class="img-fluid rounded-m">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="card card-style text-center mt-3">
            <div class="content">
                <h3>Data Izin Tidak Ditemukan</h3>
                <p>Belum ada data izin terbaru untuk ditampilkan</p>
                <a href="{{ route('home') }}" class="btn btn-full btn-m bg-highlight rounded-s mt-3">
                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
