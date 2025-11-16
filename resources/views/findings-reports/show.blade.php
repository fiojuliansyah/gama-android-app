@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <span class="header-title">Form Findings Report</span>
    <a href="{{ route('findings-reports.index') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
</div>

<div class="page-content pt-5">
    @if(isset($findingsReport) && $findingsReport)
        <div class="list-group list-custom-large">
            <div class="card card-style mb-3">
                <div class="content">

                    <!-- Logo -->
                    <div class="text-center mb-3">
                        <img src="{{ $findingsReport->site->company['logo_url'] ?? '/assets/images/empty.png' }}" alt="Logo" width="150px">
                    </div>

                    <!-- Tanggal & Dibuat Oleh -->
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Tanggal Dibuat</p>
                            <p class="line-height-s">
                                {{ optional($findingsReport->created_at)->format('d-M-Y') ?? '-' }}
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="color-theme font-15 font-800">Tanggal Laporan</p>
                            <p class="line-height-s">
                                {{ optional(\Carbon\Carbon::parse($findingsReport->date))->format('d-M-Y') ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Nama & Lokasi -->
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Dibuat Oleh</p>
                            <p class="line-height-s">{{ $findingsReport->user->name ?? '-' }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="color-theme font-15 font-800">Lokasi</p>
                            <p class="line-height-s">{{ $findingsReport->location ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Tipe & Status -->
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Tipe</p>
                            <p class="line-height-s">{{ ucfirst($findingsReport->type ?? '-') }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="color-theme font-15 font-800">Status</p>
                            <p class="line-height-s">{{ ucfirst($findingsReport->status ?? '-') }}</p>
                        </div>
                    </div>

                    <!-- Area & Jabatan -->
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Area</p>
                            <p class="line-height-s">{{ $findingsReport->user->site['name'] ?? '-' }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="color-theme font-15 font-800">Jabatan</p>
                            @if (!empty($findingsReport->user->getRoleNames()))
                                @foreach ($findingsReport->user->getRoleNames() as $role)
                                    <p class="line-height-s">{{ $role }}</p>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Deskripsi & Tindakan Langsung -->
                    <div class="mt-3 mb-3">
                        <h6>Deskripsi</h6>
                        <p>{{ $findingsReport->description ?? '-' }}</p>

                        <h6>Tindakan Langsung</h6>
                        <p>{{ $findingsReport->direct_action ?? '-' }}</p>
                    </div>

                    <!-- Approval -->
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Menyetujui</p>
                            <p class="line-height-s">{{ $findingsReport->user->leader['name'] ?? '-' }}</p>
                        </div>
                        <div class="col-6 text-end">
                            @if(isset($findingsReport->user->leader->leader))
                                <p class="color-theme font-15 font-800">Menyetujui</p>
                                <p class="line-height-s">{{ $findingsReport->user->leader->leader['name'] ?? '-' }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Image -->
                    @if($findingsReport->image_url)
                        <div class="mt-3 text-center">
                            <img src="{{ $findingsReport->image_url }}" alt="Bukti" class="img-fluid rounded-m">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="card card-style text-center mt-3">
            <div class="content">
                <h3>Data Findings Report Tidak Ditemukan</h3>
                <p>Belum ada data report terbaru untuk ditampilkan</p>
                <a href="{{ route('findings-reports.index') }}" class="btn btn-full btn-m bg-highlight rounded-s mt-3">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
