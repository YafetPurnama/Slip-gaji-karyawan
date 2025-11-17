@extends('layouts.admin')

@section('title', 'Edit Potongan Gaji')

@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Form Edit Potongan Gaji</h6>
    </div>
    <div class="card-body">
    <form action="{{ route('potongan-gaji.update', $potonganGaji->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="form-group">
      <label for="jenis_potongan">Jenis Potongan</label>
      <input type="text" name="jenis_potongan" class="form-control @error('jenis_potongan') is-invalid @enderror"
        value="{{ old('jenis_potongan', $potonganGaji->jenis_potongan) }}" required>
      @error('jenis_potongan')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
      <label for="jumlah">Jumlah Potongan (Rp)</label>
      <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
        value="{{ old('jumlah', $potonganGaji->jumlah) }}" required>
      @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <button type="submit" class="btn btn-primary">Update</button>
      <a href="{{ route('potongan-gaji.index') }}" class="btn btn-secondary">Batal</a>
    </form>
    </div>
  </div>
@endsection