@extends('layouts.app')


@section('content')
<div class="header header-fixed header-logo-center">
    <a href="index.html" class="header-title">Akun Bank</a>
    <a href="{{ route('setting') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
</div>
<div class="page-content pt-5">
        
    <div class="content mb-0">
        <form id="bank-update" class="form" action="{{ route('update.bank') }}" method="POST">
            @csrf
        <div class="input-style has-borders hnoas-icon input-style-always-active validate-field mb-4">
            <input type="name" name="bank_name" class="form-control validate-name" value="{{ $user->profile['bank_name'] ?? '' }}">
            <label for="form1" class="color-highlight font-400 font-13">Nama Bank</label>
            <i class="fa fa-times disabled invalid color-red-dark"></i>
            <i class="fa fa-check disabled valid color-green-dark"></i>
            <em>(required)</em>
        </div>
        <div class="input-style has-borders hnoas-icon input-style-always-active validate-field mb-4">
            <input type="name" name="account_name" class="form-control validate-name" value="{{ $user->profile['account_name'] ?? '' }}">
            <label for="form1" class="color-highlight font-400 font-13">Nama Akun</label>
            <i class="fa fa-times disabled invalid color-red-dark"></i>
            <i class="fa fa-check disabled valid color-green-dark"></i>
            <em>(required)</em>
        </div>
        <div class="input-style has-borders hnoas-icon input-style-always-active validate-field mb-4">
            <input type="name" name="account_number" class="form-control validate-name" value="{{ $user->profile['account_number'] ?? '' }}">
            <label for="form1" class="color-highlight font-400 font-13">No Rekening</label>
            <i class="fa fa-times disabled invalid color-red-dark"></i>
            <i class="fa fa-check disabled valid color-green-dark"></i>
            <em>(required)</em>
        </div>
        </form>                      
    </div>
    <a href="#" onclick="event.preventDefault(); document.getElementById('bank-update').submit();" class="btn btn-full btn-margins bg-highlight rounded-sm shadow-xl btn-m text-uppercase font-900">Save Information</a>
    
</div> 

@endsection