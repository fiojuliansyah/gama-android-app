@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <a href="index.html" class="header-title">Temuan (SOS)</a>
    <a href="{{ route('home') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
</div>

<div class="page-content pt-5">
    <div class="content mt-0 mb-0">
        <div class="list-group list-custom-large">
            @foreach ($reports as $report)
                <a href="{{ route('findings-reports.show', $report->id) }}">
                    <i class="fas fa-exclamation-circle font-20 
                        @if($report->type == 'low') color-green-dark 
                        @elseif($report->type == 'medium') color-yellow-dark 
                        @else color-red-dark @endif"></i>
                    <span>{{ ucfirst($report->type) }} - {{ ucfirst($report->status) }}</span>
                    <strong>{{ $report->title }}</strong>
                    <i class="fa fa-angle-right"></i>
                </a>
            @endforeach
        </div>
    </div>

    <div class="ad-300x50 ad-300x50-fixed">
        <a href="{{ route('findings-reports.create') }}" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight">
            <i class="fas fa-plus">&nbsp;</i>Buat Temuan (SOS)
        </a>
    </div>
</div>
@endsection
