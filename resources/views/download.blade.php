<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Beta: GIS - Build Terbaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Slider horizontal scroll */
        .snap-container {
            scroll-snap-type: x mandatory;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            white-space: nowrap;
            padding-right: 1rem; 
        }
        .snap-container::-webkit-scrollbar {
            display: none; /* Sembunyikan scrollbar */
        }
        .snap-container > * {
            scroll-snap-align: start;
            flex-shrink: 0;
        }
        .mobile-frame {
            max-width: 420px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans flex justify-center py-4 sm:py-8">

    <div class="mobile-frame bg-white min-h-screen">

        <div class="bg-red-600 text-white text-center py-2 text-sm font-semibold sticky top-0 z-20">
            🚨 VERSI RAHASIA INTERNAL GAMA SETIA WASPADA. JANGAN DIBAGIKAN. 🚨
        </div>

        <div class="p-4">

            <header class="flex items-center space-x-4 mb-4 pt-2">
                <div class="flex-shrink-0">
                    <img src="/assets/images/gama-trans.png" alt="GIS App Logo" class="w-16 h-16 rounded-xl shadow-md object-cover">
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 leading-snug">GIS</h1>
                    <p class="text-sm text-gray-500">Oleh GIS Dev</p>
                    <p class="text-sm text-emerald-600 font-medium">GIS Dev - Internal Build</p>
                </div>
            </header>
            
            <section class="mb-6 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="text-center">
                        <p class="text-xl font-bold text-gray-800">3+</p>
                        <p class="text-xs text-gray-500">Diunduh</p>
                    </div>
                </div>
                <a href="/assets/apk/app-release.apk" class="inline-block text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-full text-lg transition duration-300 shadow-md">
                    Install
                </a>
            </section>

            <section class="mb-6 py-2">
                <div class="snap-container flex space-x-3">
                    <img src="/assets/images/screenshot/login.png" alt="GIS Screenshot 1" class="w-64 sm:w-48 h-100 sm:h-100 object-cover rounded-lg border border-gray-200">
                    <img src="/assets/images/screenshot/home.png" alt="GIS Screenshot 2" class="w-64 sm:w-48 h-100 sm:h-100 object-cover rounded-lg border border-gray-200">
                    <img src="/assets/images/screenshot/map.jpeg" alt="GIS Screenshot 3" class="w-64 sm:w-48 h-100 sm:h-100 object-cover rounded-lg border border-gray-200">
                    <img src="/assets/images/screenshot/patroll.png" alt="GIS Screenshot 4" class="w-64 sm:w-48 h-100 sm:h-100 object-cover rounded-lg border border-gray-200">
                    <img src="/assets/images/screenshot/detail-patrol.png" alt="GIS Screenshot 5" class="w-64 sm:w-48 h-100 sm:h-100 object-cover rounded-lg border border-gray-200">
                    <img src="/assets/images/screenshot/setting.png" alt="GIS Screenshot 6" class="w-64 sm:w-48 h-100 sm:h-100 object-cover rounded-lg border border-gray-200">
                </div>
            </section>

            <section class="mt-4 pb-4 border-b border-gray-200">
                <h3 class="text-xl font-bold mb-2 text-gray-800">Tentang GIS</h3>
                <p class="text-gray-700 text-sm leading-relaxed mb-3">
                    Aplikasi GIS eksklusif untuk kebutuhan absensi dan reporting internal **GAMA SETIA WASPADA**.
                    Versi beta ini menguji integrasi Modul dan sinkronisasi data *real-time*.
                </p>
            </section>

            <section class="py-4">
                <h3 class="text-xl font-bold mb-3 text-gray-800">Yang Baru (v1.5 Beta)</h3>
                <div class="flex flex-col sm:flex-row items-start sm:space-x-3 space-y-2 sm:space-y-0">
                    <p class="text-sm text-gray-500 mt-1">17 Nov 2025</p>
                    <div class="text-sm text-gray-700 space-y-2">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Implementasi Modul Akurasi GPS (uji coba).</li>
                            <li>Perbaikan bug pada rendering offline map.</li>
                            <li>Peningkatan kinerja.</li>
                        </ul>
                        <p class="text-red-600 font-medium">Instruksi: Fokuskan pengujian pada akurasi koordinat dan sinkronisasi. Laporkan bug ke #gis-beta-gsw.</p>
                    </div>
                </div>
            </section>
        </div>

        <footer class="bg-gray-50 p-4 mt-10 text-center text-xs text-gray-500 border-t">
            <p>Aplikasi ini dikembangkan oleh GIS Dev untuk GAMA SETIA WASPADA. Akses terbatas.</p>
        </footer>

    </div>

</body>
</html>
