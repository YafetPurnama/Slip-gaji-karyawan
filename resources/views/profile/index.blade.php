@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
  <h1 class="h3 mb-4 text-gray-800">Profil Saya</h1>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <div class="row">
    <!-- Kolom Kiri: Foto Profil dan Info Dasar -->
    <div class="col-lg-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Foto Profil</h6>
        </div>
        <div class="card-body text-center">
          @php
            $foto = $user->karyawan->foto ?? null;
          @endphp
          @if($foto)
            <img class="img-fluid rounded-circle mb-3" src="{{ asset('storage/' . $foto) }}" alt="Foto Profil"
              style="width: 150px; height: 150px; object-fit: cover;">
          @else
            <img class="img-fluid rounded-circle mb-3"
              src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=150" alt="Foto Profil">
          @endif
          <h4 class="font-weight-bold">{{ $user->name }}</h4>
          <p class="text-muted">{{ $user->karyawan->jabatan->nama_jabatan ?? ucfirst($user->role) }}</p>
        </div>
      </div>
    </div>

    <!-- Kolom Kanan: Form Edit Data -->
    <div class="col-lg-8">
      <div class="card shadow mb-4">
        <div class="card-body">
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                aria-controls="profile" aria-selected="true">Edit Profil</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password"
                aria-selected="false">Ganti Password</a>
            </li>
          </ul>
          <div class="tab-content" id="myTabContent">
            <!-- Tab Edit Profil -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
              <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div class="form-group">
                  <label for="name">Nama Lengkap</label>
                  <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}"
                    required>
                </div>
                <div class="form-group">
                  <label for="email">Alamat Email</label>
                  <input type="email" id="email" name="email" class="form-control"
                    value="{{ old('email', $user->email) }}" required>
                </div>
                @if($user->role == 'pegawai')
                  <div class="form-group">
                    <label for="foto">Ganti Foto Profil</label>
                    <input type="file" id="foto" name="foto" class="form-control">
                  </div>
                @endif
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
              </form>
            </div>
            <!-- Tab Ganti Password -->
            <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
              <form action="{{ route('profile.password.update') }}" method="POST" class="mt-4">
                @csrf
                <div class="form-group">
                  <label for="current_password">Password Saat Ini</label>
                  <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="password">Password Baru</label>
                  <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="password_confirmation">Konfirmasi Password Baru</label>
                  <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    required>
                </div>
                <button type="submit" class="btn btn-primary">Ubah Password</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection