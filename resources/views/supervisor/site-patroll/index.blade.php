@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <a href="#" class="header-title">Site Patroll</a>
    <a href="{{ route('home') }}" class="header-icon header-icon-1">
        <i class="fas fa-arrow-left"></i>
    </a>
</div>

<div class="page-content pt-5">

    <div class="content mt-0 mb-0">
        <div class="list-group list-custom-large">

            @foreach ($sites as $site)
                <a href="{{ route('supervisor.site-patroll.show', $site->id) }}">
                    <i class="fas fa-map-marker-alt font-20 color-blue-dark"></i>

                    <span>{{ $site->name }}</span>
                    <strong>{{ $site->address ?? 'Alamat tidak tersedia' }}</strong>

                    <i class="fa fa-angle-right"></i>
                </a>
            @endforeach

        </div>
    </div>

</div>
@endsection
