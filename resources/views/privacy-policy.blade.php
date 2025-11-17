@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <a href="#" class="header-title">Privacy Policy</a>
    <a href="{{ route('login') }}" class="header-icon header-icon-1">
        <i class="fas fa-arrow-left"></i>
    </a>
</div>
<div class="page-content pt-5">

    <div class="content">
        <div class="d-flex flex-column gap-4">

            <div>
                <h2 class="text-center mb-4">Privacy Policy – GIS (Gama Integrated System)</h2>
                <p><strong>Effective Date:</strong> 17 November 2025</p>
                <p>GIS (Gama Integrated System) (“we”, “the app”) values the privacy of internal users and is committed to protecting employee data and company information stored in the application. This policy explains how we collect, use, store, and protect your data.</p>

                <h3>1. Information We Collect</h3>
                <ul>
                    <li><strong>Employee Data:</strong> Name, employee ID, position, department, email, phone number.</li>
                    <li><strong>HRIS Data:</strong> Attendance, leave, overtime, and performance-related information.</li>
                    <li><strong>Patrol Data:</strong> Location, time, and patrol activities recorded via QR code scans or GPS.</li>
                    <li><strong>Login Data:</strong> Username, password, and access history.</li>
                </ul>

                <h3>2. Purpose of Data Use</h3>
                <ul>
                    <li>Manage internal HRIS including attendance, leave, and overtime.</li>
                    <li>Monitor patrol activities in real-time.</li>
                    <li>Generate internal management reports.</li>
                    <li>Enhance operational security and efficiency.</li>
                </ul>

                <h3>3. Data Storage and Security</h3>
                <ul>
                    <li>All data is stored securely on internal/company servers or cloud with restricted access.</li>
                    <li>Data is protected using encryption, user authentication, and standard industry security protocols.</li>
                    <li>Only authorized personnel can access specific data based on their roles.</li>
                </ul>

                <h3>4. Data Sharing</h3>
                <p>Data will not be shared with third parties without company authorization and is solely used for internal operations.</p>

                <h3>5. User Rights</h3>
                <ul>
                    <li>Users can request access or updates to their personal data stored in the application.</li>
                    <li>Users are responsible for keeping their account credentials secure.</li>
                </ul>

                <h3>6. Policy Updates</h3>
                <p>We may update this policy periodically. Any changes will be communicated to all internal users through official company channels.</p>

                <h3>7. Contact</h3>
                <p>For any questions regarding the GIS Privacy Policy, please contact:</p>
                <p><strong>IT / HR Department</strong> – it@gamaintegratedsystem.com</p>
            </div>

            <div>
                <h2 class="text-center mb-4 mt-5">Kebijakan Privasi – GIS (Gama Integrated System)</h2>
                <p><strong>Tanggal Berlaku:</strong> 17 November 2025</p>
                <p>GIS (Gama Integrated System) (“kami”, “aplikasi”) menghargai privasi pengguna internal dan berkomitmen untuk melindungi data karyawan dan informasi perusahaan yang tersimpan di aplikasi. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi data Anda.</p>

                <h3>1. Informasi yang Dikumpulkan</h3>
                <ul>
                    <li><strong>Data Karyawan:</strong> Nama, ID karyawan, jabatan, departemen, email, nomor telepon.</li>
                    <li><strong>Data HRIS:</strong> Kehadiran, cuti, lembur, dan data terkait kinerja.</li>
                    <li><strong>Data Patrol:</strong> Lokasi, waktu, dan aktivitas patroli yang dicatat melalui scan QR code atau GPS.</li>
                    <li><strong>Data Login:</strong> Username, password, dan riwayat akses aplikasi.</li>
                </ul>

                <h3>2. Tujuan Penggunaan Data</h3>
                <ul>
                    <li>Mengelola HRIS internal termasuk absensi, cuti, dan lembur.</li>
                    <li>Memantau aktivitas patroli lapangan secara real-time.</li>
                    <li>Membuat laporan manajemen internal.</li>
                    <li>Meningkatkan keamanan dan efisiensi operasional perusahaan.</li>
                </ul>

                <h3>3. Penyimpanan dan Keamanan Data</h3>
                <ul>
                    <li>Semua data disimpan aman di server internal/perusahaan atau cloud dengan akses terbatas.</li>
                    <li>Data dilindungi menggunakan enkripsi, autentikasi pengguna, dan protokol keamanan standar industri.</li>
                    <li>Hanya pengguna yang berwenang yang dapat mengakses data sesuai dengan peran mereka.</li>
                </ul>

                <h3>4. Pembagian Data</h3>
                <p>Data tidak akan dibagikan ke pihak ketiga tanpa izin perusahaan dan hanya digunakan untuk keperluan internal.</p>

                <h3>5. Hak Pengguna</h3>
                <ul>
                    <li>Pengguna dapat meminta akses atau pembaruan data pribadi yang tersimpan di aplikasi.</li>
                    <li>Pengguna bertanggung jawab menjaga kerahasiaan akun dan kredensial login.</li>
                </ul>

                <h3>6. Perubahan Kebijakan Privasi</h3>
                <p>Kami dapat memperbarui kebijakan ini dari waktu ke waktu. Setiap perubahan akan diinformasikan kepada seluruh pengguna internal melalui saluran resmi perusahaan.</p>

                <h3>7. Kontak</h3>
                <p>Untuk pertanyaan terkait kebijakan privasi GIS, silakan hubungi:</p>
                <p><strong>Departemen IT / HR</strong> – it@gamaintegratedsystem.com</p>
            </div>

        </div>
    </div>
</div>  
@endsection
