@extends('layouts.admin')

@section('title', 'Edit Jabatan')

@push('styles')
    <style>
        .currency-input {
            text-align: right;
            padding-right: 30px !important;
        }
    </style>
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Jabatan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('jabatan.update', $jabatan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nama_jabatan">Nama Jabatan</label>
                    <input type="text" name="nama_jabatan" class="form-control @error('nama_jabatan') is-invalid @enderror"
                        value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}" required>
                    @error('nama_jabatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="gaji_pokok">Gaji Pokok</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" name="gaji_pokok" id="gaji_pokok"
                            class="form-control currency-input @error('gaji_pokok') is-invalid @enderror"
                            value="{{ old('gaji_pokok', number_format($jabatan->gaji_pokok, 0, ',', '.')) }}"
                            onkeyup="formatCurrency(this)" required>
                    </div>
                    @error('gaji_pokok')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tunjangan_transport">Tunjangan Transport</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" name="tunjangan_transport" id="tunjangan_transport"
                            class="form-control currency-input @error('tunjangan_transport') is-invalid @enderror"
                            value="{{ old('tunjangan_transport', number_format($jabatan->tunjangan_transport, 0, ',', '.')) }}"
                            onkeyup="formatCurrency(this)" required>
                    </div>
                    @error('tunjangan_transport')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="uang_makan">Uang Makan</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" name="uang_makan" id="uang_makan"
                            class="form-control currency-input @error('uang_makan') is-invalid @enderror"
                            value="{{ old('uang_makan', number_format($jabatan->uang_makan, 0, ',', '.')) }}"
                            onkeyup="formatCurrency(this)" required>
                    </div>
                    @error('uang_makan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="uang_bpjs">BPJS Ketenagakerjaan (per bulan)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" name="uang_bpjs" id="uang_bpjs"
                            class="form-control currency-input @error('uang_bpjs') is-invalid @enderror"
                            value="{{ old('uang_bpjs', number_format($jabatan->uang_bpjs ?? 0, 0, ',', '.')) }}"
                            onkeyup="formatCurrency(this)" required>
                    </div>
                    @error('uang_bpjs')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="uang_lembur">Uang Lembur (per hari)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" name="uang_lembur" id="uang_lembur"
                            class="form-control currency-input @error('uang_lembur') is-invalid @enderror"
                            value="{{ old('uang_lembur', number_format($jabatan->uang_lembur ?? 0, 0, ',', '.')) }}"
                            onkeyup="formatCurrency(this)" required>
                    </div>
                    @error('uang_lembur')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('jabatan.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            input.value = value;
            input.setAttribute('data-raw', input.value.replace(/\./g, ''));
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const currencyInputs = document.querySelectorAll('.currency-input');
            currencyInputs.forEach(input => {
                const rawValue = input.value.replace(/\./g, '');
                input.value = rawValue;
            });
        });

        // Format inputs on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currencyInputs = document.querySelectorAll('.currency-input');
            currencyInputs.forEach(input => {
                if (input.value) {
                    const value = input.value.replace(/\D/g, '');
                    input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            });
        });
    </script>
@endpush