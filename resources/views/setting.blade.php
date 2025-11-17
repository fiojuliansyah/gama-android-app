@extends('layouts.master')


@section('content')
<div class="header header-fixed header-logo-center">
    <a href="index.html" class="header-title">Setting</a>
</div>
<div class="page-content pt-5">


    <div class="content">
        <div class="d-flex">
            <div>
                <img src="{{ Auth::user()->profile->avatar_url ?? '/mobile/images/avatars/2m.png' }}" width="50" class="me-3 bg-highlight rounded-xl">
            </div>
            <div>
                <h1 class="mb-0 pt-1">{{ Auth::user()->name }}</h1>
                <p class="color-highlight font-11 mt-n2 mb-3">{{ Auth::user()->email }}</p>
            </div>
            <div style="margin-left: 10px; padding-top: 5px;">
                <a href="{{ route('account') }}">
                    <i class="fas fa-pencil-alt color-dark-dark"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="content mb-2">
        <h5 class="float-start font-16 font-500">Informasi Akun</h5>
        <div class="clearfix"></div>
    </div>
    <div class="content mt-0 mb-0">
        <div class="list-group list-custom-large me-2">
            <a href="{{ route('profile') }}">
                <i class="fas fa-address-card font-20 color-dark-dark"></i>
                <span>Profil</span>
                <strong>Profile</strong>
                <i class="fa fa-angle-right color-dark-dark"></i>
            </a>
            <a href="{{ route('bank') }}">
                <i class="fa fa-bank font-20 color-dark-dark"></i>
                <span>Informasi BANK</span>
                <strong>BANK Information</strong>
                <i class="fa fa-angle-right color-dark-dark"></i>
            </a>
            {{-- <a href="{{ route('esign') }}">
                <i class="fas fa-signature font-20"></i>
                <span>Tanda tangan digital</span>
                <strong>Digital signature</strong>
                <span class="badge bg-red-dark me-2">NEW</span>
                <i class="fa fa-angle-right"></i>
            </a> --}}
            <a href="#">
                <i class="fas fa-user-shield font-20 color-dark-dark"></i>
                <span>PIN</span>
                <strong>PIN</strong>
                <i class="fa fa-angle-right color-dark-dark"></i>
            </a>
        </div>  
    </div>
    <div class="content mb-2 mt-5">
        <h5 class="float-start font-16 font-500">Info lainnya</h5>
        <div class="clearfix"></div>
    </div>
    <div class="content mt-0 mb-0">
        <div class="list-group list-custom-large me-2">
            <a data-menu="menu-task-item" href="#">
                <i class="fas fa-shield-alt font-20 color-dark-dark"></i>
                <span>Kebijakan Privasi</span>
                <strong>Privacy Police</strong>
                <i class="fa fa-angle-right color-dark-dark"></i>
            </a>
            <a data-menu="menu-task-item" href="#">
                <i class="fas fa-book font-20 color-dark-dark"></i>
                <span>Cara Penggunaan Aplikasi</span>
                <strong>How to User</strong>
                <i class="fa fa-angle-right color-dark-dark"></i>
            </a>
            <a data-menu="menu-task-item" href="#">
                <i class="fas fa-bug font-20 color-dark-dark"></i>
                <span>Lapor Error</span>
                <strong>Report Bug</strong>
                <i class="fa fa-angle-right color-dark-dark"></i>
            </a>
            <a data-toggle-theme data-trigger-switch="switch-21" href="#">
                <i class="fa font-19 fa-lightbulb rounded-s color-dark-dark"></i>
                <span>Dark Mode</span>
                <strong>Turn off the Lights</strong>
                <div class="custom-control scale-switch android-switch mt-n1 pt-3">
                    <input data-toggle-theme type="checkbox" class="android-input" id="switch-21">
                    <label class="custom-control-label" for="switch-21"></label>
                </div>
                <i class="fa fa-chevron-right opacity-30"></i>
            </a>
            <a data-menu="menu-task-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt font-20 color-dark-dark"></i>
                <span>Keluar</span>
                <strong>Logout</strong>
            </a>
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                @csrf
            </form>
        </div>  
    </div>
</div>  
@endsection