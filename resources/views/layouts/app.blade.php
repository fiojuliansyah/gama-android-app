<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
<title>Treework Mobile App</title>
<link rel="stylesheet" type="text/css" href="/assets/styles/bootstrap.css">
<link rel="stylesheet" type="text/css" href="/assets/styles/style.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/assets/fonts/css/fontawesome-all.min.css">
<link rel="manifest" href="/_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
<link rel="apple-touch-icon" sizes="180x180" href="/assets/app/icons/icon-192x192.png">
@stack('css')
</head>

<body class="theme-light" data-highlight="highlight-red" data-gradient="body-default">

<div id="page">
    @if (session('success'))
        <div id="custom-notification" class="toast w-auto notification shadow-xl notch-push mt-3" 
            style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: block; opacity: 1;">
            <div class="toast-header bg-blue-dark border-blue-dark">
                <strong class="me-auto color-white">System</strong>
                <small class="color-white opacity-50 me-2">just now</small>
                <button type="button" onclick="this.parentElement.parentElement.style.display='none'" 
                        class="ms-2 mb-1 color-white close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body bg-blue-dark color-white">
                {{ session('success') }}
            </div>
        </div>

        <script>
            // Auto-hide after 3 seconds
            setTimeout(function() {
                var notification = document.getElementById('custom-notification');
                if (notification) notification.style.display = 'none';
            }, 3000);
        </script>
    @endif

    @yield('content')

    @yield('modal')
</div>

<script type="text/javascript" src="/assets/scripts/bootstrap.min.js"></script>
<script type="text/javascript" src="/assets/scripts/custom.js"></script>
@stack('js')
</body>
