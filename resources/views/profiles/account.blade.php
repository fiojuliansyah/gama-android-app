@extends('layouts.app')


@section('content')
<div class="header header-fixed header-logo-center">
    <a href="index.html" class="header-title">Pengaturan Akun</a>
    <a href="{{ route('setting') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
</div>
<div class="page-content pt-5">
    <div class="content mb-0">
        <form id="account-update" class="form" action="{{ route('update.account') }}" method="POST">
            @csrf
        <div class="input-style has-borders hnoas-icon input-style-always-active validate-field mb-4">
            <input type="name" name="name" class="form-control validate-name" value="{{ $user->name }}">
            <label for="form1" class="color-highlight font-400 font-13">Name</label>
            <i class="fa fa-times disabled invalid color-red-dark"></i>
            <i class="fa fa-check disabled valid color-green-dark"></i>
            <em>(required)</em>
        </div>
        <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
            <input type="email" name="email" class="form-control validate-email" value="{{ $user->email }}"">
            <label for="form2" class="color-highlight font-400 font-13">Email</label>
            <i class="fa fa-times disabled invalid color-red-dark"></i>
            <i class="fa fa-check disabled valid color-green-dark"></i>
            <em>(required)</em>
        </div>
        <div class="input-style has-borders hnoas-icon input-style-always-active validate-field mb-4">
            <input type="name" name="nik" class="form-control validate-name" value="{{ $user->nik }}">
            <label for="form1" class="color-highlight font-400 font-13">NIK KTP</label>
            <i class="fa fa-times disabled invalid color-red-dark"></i>
            <i class="fa fa-check disabled valid color-green-dark"></i>
            <em>(required)</em>
        </div>
        <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
            <input type="tel" name="phone" class="form-control validate-tel" value="{{ $user->phone }}">
            <label for="form3" class="color-highlight font-400 font-13">Phone Number</label>
            <i class="fa fa-times disabled invalid color-red-dark"></i>
            <i class="fa fa-check disabled valid color-green-dark"></i>
            <em>(required)</em>
        </div>
        <div class="input-style has-borders no-icon input-style-always-active validate-field mb-4">
            <input type="passord" name="password" class="form-control validate-passord" id="form4" placeholder="******">
            <label for="form4" class="color-highlight font-400 font-13">Password</label>
            <i class="fa fa-times disabled invalid color-red-dark"></i>
            <i class="fa fa-check disabled valid color-green-dark"></i>
            <em>(required)</em>
        </div>
        </form>                      
    </div>
    <a href="#" onclick="event.preventDefault(); document.getElementById('account-update').submit();" class="btn btn-full btn-margins bg-highlight rounded-sm shadow-xl btn-m text-uppercase font-900">Save Information</a>
    
</div> 

@endsection