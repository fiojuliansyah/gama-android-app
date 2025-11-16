@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="card card-style" style="margin-top: 50px">
        <div class="page-title page-title-small">
            <h2><a href="#" data-back-button><i class="fa fa-arrow-left" style="color: black;">&nbsp;&nbsp;&nbsp;</i><span><img src="{{ $minute->site->company['logo_url'] }}" width="150px"></span></h2>
        </div>
        <div class="content">
            <div class="divider mt-n4 mb-3"></div>
            <center>
                <h6>FORM BERITA ACARA</h6>
            </center>
            <br>
            <div class="row">
                <div class="col-6">
                    <p class="color-theme font-15 font-800">Tanggal Kehadiran</p>
                </div>
                <div class="col-6">
                    <p class="color-theme font-15 font-800 text-right">Tanggal Dibuat</p>
                </div>

                <div class="col-12 mb-n4 mt-n4"></div>

                <div class="col-6">
                    <p class="line-height-s">{{ $minute->date->format('d-m-Y') }}</p>
                </div>
                <div class="col-6">
                    <p class="line-height-s text-right">{{ $minute->created_at->format('d-m-Y') }}</p>
                </div>

                <div class="col-12 mb-3"></div>
                <div class="col-6">
                    <p class="color-theme font-15 font-800">Dibuat Oleh</p>
                </div>
                <div class="col-6">
                    <p class="color-theme font-15 font-800 text-right">Jam Kehadiran</p>
                </div>

                <div class="col-12 mb-n4 mt-n4"></div>

                <div class="col-6">
                    <p class="line-height-s">{{ $minute->user['name'] }}</p>
                </div>
                <div class="col-6">
                    <p class="line-height-s text-right">{{ $minute->clock_in }} - {{ $minute->clock_out }}</p>
                </div>

                <div class="col-12 mb-3"></div>
                <div class="col-6">
                    <p class="color-theme font-15 font-800">Area</p>
                </div>
                <div class="col-6">
                    <p class="color-theme font-15 font-800 text-right">Jabatan</p>
                </div>

                <div class="col-12 mb-n4 mt-n4"></div>

                <div class="col-6">
                    <p class="line-height-s">{{ $minute->user->site['name'] }}</p>
                </div>
                <div class="col-6">
                    @if (!empty($minute->user->getRoleNames()))
                        @foreach ($minute->user->getRoleNames() as $v)
                            <p class="line-height-s text-right">{{ $v }}</p>
                        @endforeach
                    @endif
                </div>

            </div>
            <center class="mb-4">
                <div class="row">
                    <div class="col-6">
                        <img src="{{ $minute->imagein_url }}" alt="" width="100px">
                    </div>
                    <div class="col-6">
                        <img src="{{ $minute->imageout_url }}" alt="" width="100px">
                    </div>
                </div>
            </center>
            <h4 class="mb-n1">Deskripsi</h4>
            <p>
                {{ $minute->remark }}
            </p>

            <div class="divider"></div>
            <div class="col-12 mb-5 mt-5"></div>
            <div class="row">
                <div class="col-12 mb-3"></div>
                <div class="col-6">
                    <div class="divider mt-1 mb-1"></div>
                    <p class="color-theme">{{ $minute->user->leader['name'] }}</p>
                </div>
                @if ($minute->user->leader->leader)   
                <div class="col-6">
                    <div class="divider mt-1 mb-1"></div>
                    <p class="color-theme text-right">{{ $minute->user->leader->leader['name'] }}</p>
                </div>
                @else
                @endif

                <div class="col-12 mb-n4 mt-n4"></div>

                <div class="col-6">
                    @if (!empty($minute->user->leader->getRoleNames()))
                        @foreach ($minute->user->leader->getRoleNames() as $v)
                            <p class="line-height-s font-15 font-800">{{ $v }}</p>
                        @endforeach
                    @endif
                </div>
                @if ($minute->user->leader->leader)    
                <div class="col-6">
                    @if (!empty($minute->user->leader->leader->getRoleNames()))
                        @foreach ($minute->user->leader->leader->getRoleNames() as $v)
                            <p class="line-height-s font-15 font-800 text-right">{{ $v }}</p>
                        @endforeach
                    @endif
                </div>
                @else
                @endif

                <div class="col-12 mb-n4 mt-n4"></div>

                <div class="col-6">
                    <p class="color-theme font-15 font-800">Menyetujui</p>
                </div>
                @if ($minute->user->leader->leader) 
                <div class="col-6">
                    <p class="color-theme font-15 font-800 text-right">Menyetujui</p>
                </div>    
                @else
                @endif
            </div>
        </div>
    </div>

    <a href="#" class="btn btn-full btn-margins  bg-highlight btn-m text-uppercase font-900 rounded-s shadow-xl">
        <i class="fas fa-cloud-download-alt">&nbsp;</i>Download Berita Acara
     </a>
</div>

@endsection