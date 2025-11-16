@extends('layouts.app')

@section('content')
    <div class="header header-fixed header-logo-center">
        <a href="/" class="header-title">Transaksi Valet</a>
        <a href="{{ route('valet.index') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
    </div>
    <div class="page-content header-clear-medium">

        <div class="text-center">
            <h3>Scan QR Code to pay</h3>
            <div class="qrcode-container">
                {!! $valet->q_code !!} 
            </div>
            <p>ID Transaction: {{ $valet->transaction_id }}</p>
            <p>Nama: {{ $valet->name }}</p>
            <p>Transaction Amount: {{ $valet->amount }}</p>
        </div>
    </div>
@endsection
