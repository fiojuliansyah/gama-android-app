@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <a href="index.html" class="header-title">Berita Acara</a>
    <a href="{{ route('home') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
</div>
<div class="page-content pt-5">
        
    <div class="content mt-0 mb-0">
        <div class="list-group list-custom-large">
            @foreach ($minutes as $minute)    
                <a href="{{ route('minute.show', $minute->id) }}">
                    <i class="fas fa-file-alt font-20 color-green-dark"></i>
                    <span>{{ $minute->type }}</span>
                    <strong>{{ $minute->remark }}</strong>
                    <i class="fa fa-angle-right"></i>
                </a>
            @endforeach
        </div>
    </div>
    <div class="ad-300x50 ad-300x50-fixed">
        <a href="{{ route('minute.create') }}" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight">
            <i class="fas fa-plus">&nbsp;</i>Buat Berita Acara
        </a>
    </div>
</div>  
@endsection