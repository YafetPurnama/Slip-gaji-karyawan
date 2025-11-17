@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
  <div class="mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    {{-- Menampilkan Hari dan Tanggal sesuai format di gambar --}}
    <p class="mb-0 text-secondary">{{ \Carbon\Carbon::now()->translatedFormat('l, d-m-Y') }}</p>
  </div>

  <div class="row">

    <div class="col-lg-3 col-md-6 mb-4">
    <div class="card card-stats border-left-primary shadow-sm h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
      <div>
        <div class="text-xs text-primary text-uppercase mb-1">Data Pegawai</div>
        <div class="h5 mb-0 font-weight-bold">{{ $jumlahPegawai }}</div>
      </div>
      <div class="col-auto">
        <i class="fas fa-users fa-2x text-gray-300"></i>
      </div>
      </div>
    </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
    <div class="card card-stats border-left-success shadow-sm h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
      <div>
        <div class="text-xs text-success text-uppercase mb-1">Data Admin</div>
        <div class="h5 mb-0 font-weight-bold">{{ $jumlahAdmin }}</div>
      </div>
      <div class="col-auto">
        <i class="fas fa-user-cog fa-2x text-gray-300"></i>
      </div>
      </div>
    </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
    <div class="card card-stats border-left-info shadow-sm h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
      <div>
        <div class="text-xs text-info text-uppercase mb-1">Data Jabatan</div>
        <div class="h5 mb-0 font-weight-bold">{{ $jumlahJabatan }}</div>
      </div>
      <div class="col-auto">
        <i class="fas fa-briefcase fa-2x text-gray-300"></i>
      </div>
      </div>
    </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
    <div class="card card-stats border-left-warning shadow-sm h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
      <div>
        <div class="text-xs text-warning text-uppercase mb-1">Data Kehadiran</div>
        <div class="h5 mb-0 font-weight-bold">{{ $jumlahKehadiran }}</div>
      </div>
      <div class="col-auto">
        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
      </div>
      </div>
    </div>
    </div>
  </div>
@endsection

@push('styles')
  <style>
    .card-stats {
    border-radius: .5rem;
    transition: all 0.3s ease-in-out;
    }

    .card-stats:hover {
    transform: translateY(-5px);
    box-shadow: 0 .4rem .9rem 0 rgba(0, 0, 0, .1) !important;
    }

    .shadow-sm {
    box-shadow: 0 .125rem .25rem 0 rgba(0, 0, 0, .075) !important;
    }

    .text-xs {
    font-size: .8rem;
    font-weight: 700;
    }

    .text-gray-300 {
    color: #e3e6f0;
    }

    .border-left-primary {
    border-left: 4px solid #4e73df;
    }

    .text-primary {
    color: #4e73df !important;
    }

    .border-left-success {
    border-left: 4px solid #1cc88a;
    }

    .text-success {
    color: #1cc88a !important;
    }

    .border-left-info {
    border-left: 4px solid #36b9cc;
    }

    .text-info {
    color: #36b9cc !important;
    }

    .border-left-warning {
    border-left: 4px solid #f6c23e;
    }

    .text-warning {
    color: #f6c23e !important;
    }
  </style>
@endpush