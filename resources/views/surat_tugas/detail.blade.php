@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">✍️ Detail Surat Tugas</h3>

    <form action="{{ route('surat_tugas.update', $suratTugas->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card shadow-sm p-4">
            <div class="mb-3">
                <label class="form-label">Nomor Surat</label>
                <input type="text" name="nomor_surat" class="form-control"
                    value="{{ old('nomor_surat', $suratTugas->nomor_surat) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Petugas</label>
                <input type="text" name="nama_petugas" class="form-control"
                    value="{{ old('nama_petugas', $suratTugas->nama_petugas) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal_pelaksanaan" class="form-control"
                    value="{{ old('tanggal_pelaksanaan', $suratTugas->tanggal_pelaksanaan) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tujuan Tugas</label>
                <textarea name="tujuan_tugas" class="form-control" rows="3" required>{{ old('tujuan_tugas', $suratTugas->tujuan_tugas) }}</textarea>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-success">💾 Simpan</button>
            <a href="{{ route('surat_tugas.preview', $suratTugas->id) }}" class="btn btn-secondary">👁️ Preview</a>
        </div>
    </form>
</div>
@endsection
