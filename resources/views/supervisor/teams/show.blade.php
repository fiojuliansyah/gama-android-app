@extends('layouts.app')

@section('content')

<div class="header header-fixed header-logo-center">
    <a href="#" onclick="history.back()" class="header-icon header-icon-1">
        <i class="fas fa-arrow-left"></i>
    </a>
    <span class="header-title">Detail User</span>
</div>

<div class="page-content p-3 pt-5">

    <div class="p-4 text-center">
        <img src="{{ $user->profile->avatar_url ?? '/assets/images/avatars/2m.png' }}"
            class="rounded-circle mb-3" width="90" height="90">

        <h3 class="mb-1">{{ $user->name }}</h3>

        <div class="text-muted mb-3 small">
            <div>{{ $user->email }}</div>
            <div>{{ $user->phone ?? 'N/A' }}</div>
        </div>

        <div class="divider my-3"></div>

        {{-- Info Utama --}}
        <div class="small">

            <div class="d-flex justify-content-between mb-2">
                <strong>Site</strong>
                <span>{{ $user->site->name ?? '-' }}</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Role</strong>
                <span>
                    @foreach ($user->getRoleNames() as $role)
                        {{ $role }}
                    @endforeach
                </span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Bergabung</strong>
                <span>
                    {{ $user->profile->join_date 
                        ? \Carbon\Carbon::parse($user->profile->join_date)->format('d M Y') 
                        : '-' }}
                </span>
            </div>

        </div>

        <div class="divider my-3"></div>

        {{-- Profile Lengkap --}}
        <h5 class="text-start mb-3">Informasi Profile</h5>

        <div class="small">

            <div class="d-flex justify-content-between mb-2">
                <strong>NIK</strong>
                <span>{{ $user->profile->employee_nik ?? '-' }}</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Jenis Kelamin</strong>
                <span>{{ $user->profile->gender ?? '-' }}</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Tempat Lahir</strong>
                <span>{{ $user->profile->birth_place ?? '-' }}</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Tanggal Lahir</strong>
                <span>
                    {{ $user->profile->birth_date 
                        ? \Carbon\Carbon::parse($user->profile->birth_date)->format('d M Y')
                        : '-' }}
                </span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Status Pernikahan</strong>
                <span>{{ $user->profile->marriage_status ?? '-' }}</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Jumlah Anak</strong>
                <span>{{ $user->profile->number_of_children ?? '-' }}</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Alamat</strong>
                <span class="text-end" style="max-width: 60%;">
                    {{ $user->profile->address ?? '-' }}
                </span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Status Karyawan</strong>
                <span>{{ $user->profile->employee_status ?? '-' }}</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>NPWP</strong>
                <span>{{ $user->profile->npwp_number ?? '-' }}</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Bank</strong>
                <span>{{ $user->profile->bank_name ?? '-' }}</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <strong>Rekening</strong>
                <span>{{ $user->profile->account_number ?? '-' }}</span>
            </div>

        </div>
        <div class="mt-4 text-center">
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#resignModal">
                Resign User
            </button>
        </div>

    </div>
    <div class="modal fade" id="resignModal" tabindex="-1" aria-labelledby="resignModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form action="{{ route('supervisor.teams.resign', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="resignModalLabel">Konfirmasi Resign</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-2">Masukkan tanggal resign untuk user:</p>

                        <input type="date" name="resign_date" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-danger">
                            Simpan Resign
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

@endsection
