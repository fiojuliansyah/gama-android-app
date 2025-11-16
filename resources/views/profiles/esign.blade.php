@extends('layouts.module')

@section('content')
<div class="page-content">
    <div class="page-title page-title-small">
        <h2 style="color: black">
            <a href="#" data-back-button><i class="fa fa-arrow-left" style="color: black"></i></a>Esign
        </h2>
        <div class="divider"></div>
    </div>
    <div class="content mb-0">
        <h5 class="font-16 font-500">Silahkan masukan tanda tangan dibawah ini.</h5>
        <form id="esign-update" class="form" action="{{ route('update.esign') }}" method="POST">
            @csrf
            <div style="border: 1px solid #eef2f1; border-radius: 8px; padding: 10px; box-sizing: border-box;">
                <canvas id="signatureCanvas" width="360" style="border: 1px solid #000;"></canvas>
            </div>
            <!-- Hidden input to store the signature data URL -->
            <input type="hidden" name="esign" id="esignData" value="">
        </form>
    </div>
    <a href="#" onclick="event.preventDefault(); saveSignature();" class="btn btn-full btn-margins bg-highlight rounded-sm shadow-xl btn-m text-uppercase font-900">Save Signature</a>
    <div class="content mt-5">
        <h5 class="font-16 font-500">tanda tangan digital anda.</h5>
        <img src="{{ $user->profile['esign_url'] ?? '' }}" width="300" alt="Existing Signature">
    </div>
    @endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@3.0.0/signature_pad.js" data-navigate-track></script>
<script data-navigate-track>
    document.addEventListener('DOMContentLoaded', function() {
        var canvas = document.getElementById('signatureCanvas');
        var signaturePad = new SignaturePad(canvas);

        window.saveSignature = function() {
            if (signaturePad.isEmpty()) {
                alert("Please provide a signature.");
                return;
            }

            var dataURL = signaturePad.toDataURL(); // Convert canvas to data URL
            document.getElementById('esignData').value = dataURL; // Set data URL to hidden input

            // Submit the form
            document.getElementById('esign-update').submit();
        };
    });
</script>
@endpush
