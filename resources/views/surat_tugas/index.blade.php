@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">📄 Daftar Surat Tugas</h3>

    @if(Auth::check() && Auth::user()->isRole('kepala_biro'))
    <div class="mb-3">
        <a href="{{ route('surat_tugas.create') }}" class="btn btn-primary">+ Buat Surat Tugas</a>
    </div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nomor Pesanan</th>
                <th>Penera Ditugaskan</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th width="220">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suratTugas as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nomor_pesanan }}</td>
                    <td>
@foreach($item->peneraTugas ?? [] as $p)
    <span class="badge bg-info text-dark">{{ $p->nama_penera }}</span>
@endforeach
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-{{ $item->status == 'Selesai' ? 'success' : 'secondary' }}">
                            {{ $item->status ?? 'Draft' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('surat_tugas.show', $item->id) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('surat_tugas.preview', $item->id) }}" class="btn btn-sm btn-secondary">Preview</a>

                        @if(Auth::user()->isRole('kepala_biro'))
                            <form action="{{ route('surat_tugas.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Hapus surat tugas ini?')">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada surat tugas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
