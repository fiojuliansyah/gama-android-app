@extends('layouts.app')

@section('content')

<div class="header header-fixed header-logo-center">
    <span class="header-title">Security Patroll</span>
    <a href="{{ route('home') }}" class="header-icon header-icon-1">
        <i class="fas fa-arrow-left"></i>
    </a>
</div>

<div class="page-content pt-5">

    {{-- FILTER SESSION --}}
    <div class="mb-4">
        <div class="content">
            <form method="GET" action="{{ route('patroll.index') }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 font-600">Filter Session</h5>
                    </div>

                    <div>
                        <select name="session_id"
                                onchange="this.form.submit()"
                                class="form-control font-13"
                                style="padding: 4px 8px;">
                            
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}"
                                    {{ $session && $session->id == $s->id ? 'selected' : '' }}>
                                    Session {{ $s->patroll_code }} ({{ $s->date }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($floors->count() == 0)
        <div class="card card-style text-center mt-4">
            <div class="content">
                <h4>Tidak Ada Data Lantai</h4>
                <p>Belum ada data lantai untuk area Anda.</p>
            </div>
        </div>
    @else

        <div class="row px-3 pt-4">

            @foreach($floors as $floor)

                @php
                    if (!$sessionToday) {
                        $color = '#E74C3C';
                        $statusText = 'Belum Dipatroli';
                        $statusColor = '#E74C3C';

                    } else {
                        $isPatrolled = in_array($floor->id, $patrolledFloors);

                        if ($isPatrolled) {
                            $color = '#2ECC71';
                            $statusText = 'Sudah Dipatroli';
                            $statusColor = '#2ECC71';
                        } else {
                            $color = '#E74C3C';
                            $statusText = 'Belum Dipatroli';
                            $statusColor = '#E74C3C';
                        }
                    }
                @endphp

                <div class="col-6 mb-3">
                    <a href="#"
                        class="d-block text-center p-3"
                        style="border: 1px solid #e5e5e5; border-radius: 8px;">

                        <i class="fas fa-layer-group font-30 color-blue-dark mb-2"></i>

                        <div class="mt-2 mb-1">
                            <span style="
                                display:inline-block;
                                width:16px;
                                height:16px;
                                border-radius:50%;
                                background: {{ $color }};
                            "></span>
                        </div>

                        <p class="font-12 mb-2" style="color: {{ $statusColor }}">
                            {{ $statusText }}
                        </p>

                        <h5 class="font-600 mb-1">{{ $floor->name }}</h5>

                        <p class="font-11 color-gray mb-0">
                            {{ $floor->description ?? 'Tidak ada deskripsi' }}
                        </p>

                    </a>
                </div>

            @endforeach

        </div>

    @endif
    <div class="ad-300x50 ad-300x50-fixed">
        <a href="{{ route('patroll.scan') }}" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight">
            <i class="fas fa-plus">&nbsp;</i>Start Patroli
        </a>
    </div>

</div>

@endsection
