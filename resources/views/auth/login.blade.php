@extends('layouts.app')

@section('content')
<div class="page-content d-flex flex-column justify-content-center min-vh-100">
    <div class="content text-center">
        <div class="page-title page-title-small">
            <img src="/assets/images/gama-trans.png" width="200px">
        </div>
    </div>
    <div class="content mb-0 text-center">
        <form id="login-form" class="form w-100" method="POST" action="{{ route('login') }}" onsubmit="return disableSubmit();">
            @csrf
            <div class="input-style no-borders has-icon validate-field mb-4">
                <input type="text" name="login" class="form-control validate-name" placeholder="Nomor ID Card">
                <label class="color-blue-dark font-10 mt-1">Nomor ID Card</label>
            </div>
            <div class="row">
                <div class="col-10">
                    <div class="input-style no-borders has-icon validate-field mb-4">
                        <input type="password" name="password" id="password" class="form-control validate-password" placeholder="Password">
                        <label class="color-blue-dark font-10 mt-1">Password</label>
                    </div>
                </div>
                <div class="col-2 d-flex align-items-center justify-content-center">
                    <span class="cursor-pointer" onclick="togglePasswordVisibility()">
                        <i id="eye-icon" class="fa fa-eye-slash"></i>
                    </span>
                </div>
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <em style="color: red">{{ $error }}</em>
                    @endforeach
                @endif
            </div>
        </form>

        <a href="#" id="login-btn" onclick="submitForm();" class="btn btn-m mt-4 mb-2 btn-full bg-highlight rounded-sm text-uppercase font-900">Login</a>

        <!-- Privacy Policy Link -->
        <div class="mt-2">
            <a href="{{ route('privacy-policy') }}" class="font-12 color-blue-dark">Privacy Policy / Kebijakan Privasi</a>
        </div>
    </div>

    <div id="loader" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 30;">
        <div class="spinner" style="border: 8px solid #f3f3f3; border-top: 8px solid #020EBD; border-radius: 50%; width: 60px; height: 60px; animation: spin 1s linear infinite;"></div>
    </div>
</div>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@push('js')
<script>
    let formSubmitted = false;

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    }

    function submitForm() {
        if (!formSubmitted) {
            formSubmitted = true;

            // Nonaktifkan tombol login
            const loginBtn = document.getElementById('login-btn');
            loginBtn.disabled = true;
            loginBtn.innerHTML = 'Loading...';

            // Tampilkan loader
            const loader = document.getElementById('loader');
            loader.style.display = 'block';

            // Kirim form
            document.getElementById('login-form').submit();
        }
    }

    function disableSubmit() {
        // Jika form sudah dikirim, matikan pengiriman ulang
        if (formSubmitted) {
            return false;
        }
        return true;
    }
</script>
@endpush
