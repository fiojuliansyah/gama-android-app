@extends('layouts.app')

@section('content')
<div class="header header-fixed header-logo-center">
    <span class="header-title">Payslip</span>
    <a href="{{ route('home') }}" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
</div>

<div class="page-content pt-5">
    @if(isset($payroll) && $payroll)
        <div class="list-group list-custom-large">
            <div class="card card-style mb-3">
                <div class="content">
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Dibuat Oleh</p>
                            <p class="line-height-s">{{ $payroll->user->site->company->name }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="color-theme font-15 font-800">Tanggal Dibuat</p>
                            <p class="line-height-s">{{ $payroll->created_at->format('d-m-Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Nama</p>
                            <p class="line-height-s">{{ $payroll->user->name }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="color-theme font-15 font-800">Jabatan</p>
                            @if (!empty($payroll->user->getRoleNames()))
                                @foreach ($payroll->user->getRoleNames() as $role)
                                    <p class="line-height-s">{{ $role }}</p>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="color-theme font-15 font-800">Area</p>
                            <p class="line-height-s">{{ $payroll->user->site['name'] }}</p>
                        </div>
                    </div>

                    <!-- Table Payslip -->
                    <div class="table-responsive mt-3">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Basic Salary</td>
                                    <td class="text-end">{{ number_format($payroll->salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Allowances</td>
                                    <td class="text-end">{{ number_format($payroll->allowance_fix, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="2" class="bg-light text-secondary">Potongan</th>
                                </tr>
                                <tr>
                                    <td>Iuran Hari Tua</td>
                                    <td class="text-end">{{ number_format($payroll->jht_employee, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Iuran Pensiun</td>
                                    <td class="text-end">{{ number_format($payroll->jp_employee, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Iuran Kesehatan</td>
                                    <td class="text-end">{{ number_format($payroll->kes_employee, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Potongan Lain</td>
                                    <td class="text-end">{{ number_format($payroll->deduction_fix, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Potongan Telat</td>
                                    <td class="text-end">{{ number_format($payroll->late_time_deduction, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Potongan Alpha</td>
                                    <td class="text-end">{{ number_format($payroll->alpha_time_deduction, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Pph21</td>
                                    <td class="text-end">{{ number_format($payroll->pph21, 2) }}</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>Net Pay</td>
                                    <td class="text-end">{{ number_format($payroll->take_home_pay, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
    @else
        <div class="card card-style text-center mt-3">
            <div class="content">
                <h3>Data Payslip Tidak Ditemukan</h3>
                <p>Belum ada data payslip terbaru untuk ditampilkan</p>
                <a href="{{ route('home') }}" class="btn btn-full btn-m bg-highlight rounded-s mt-3">
                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
    