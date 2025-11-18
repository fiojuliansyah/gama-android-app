@extends('layouts.app')

@section('content')
    <div class="header header-fixed header-logo-center">
        <a href="index.html" class="header-title">Teams</a>
        <a href="{{ route('home') }}" class="header-icon header-icon-1">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <div class="page-content pt-5 px-3">
        <form method="GET" class="mb-4 pt-5">
            <div class="row">
                <div class="col-12 mb-2">
                    <select name="site_id" id="site_id" class="form-control">
                        <option value="">Semua Site</option>
                        @foreach ($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau nomor"
                        value="{{ request('search') }}">
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-primary btn-sm">Filter</button>
                </div>
            </div>
        </form>

        @if ($teams->isEmpty())
            <div class="alert alert-warning">
                Tidak ada user yang ditemukan di Site yang Anda pimpin.
            </div>
        @endif

        <div class="row mt-5">
            @foreach ($teams as $user)
                <div class="col-6 mb-3">
                    <a href="{{ route('supervisor.teams.show', $user->id) }}">
                        <div
                            class="d-flex flex-column align-items-center justify-content-center text-center p-4 border rounded">
                            <img src="{{ $user->profile->avatar_url ?? '/assets/images/avatars/2m.png' }}"
                                class="rounded-circle mb-3" width="60" height="60">
                            <h4 class="mb-2">{{ $user->name }}</h4>
                            <div class="text-muted small">
                                <div>{{ $user->email }}</div>
                                <div>{{ $user->phone ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

    </div>
@endsection
